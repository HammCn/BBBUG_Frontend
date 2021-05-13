<template>
    <div id="app">
        <div class="bbbug_bg" :style="{backgroundImage:'url('+getStaticUrl(background)+')'}">
        </div>
        <!-- <div class="snow"></div> -->
        <div class="bbbug_link">
            <a href="https://doc.bbbug.com" target="_blank">开发文档</a>
            <a href="https://gitee.com/bbbug_com" target="_blank">Gitee</a>
            <a href="https://github.com/HammCn" target="_blank">Github</a>
        </div>
        <div class="bbbug_main">
            <div class="bbbug_main_box" v-if="roomInfo && userInfo" v-loading="appLoading">
                <div class="bbbug_main_menu">
                    <div class="bbbug_main_menu_head" @click="openMySetting" title="我的个人中心"><img class="xiaomi"
                            :src="userInfo ? getStaticUrl(userInfo.user_head) : getStaticUrl('new/images/nohead.jpg')"
                            :onerror="getStaticUrl('new/images/nohead.jpg')" />
                    </div>
                    <div v-if="roomInfo.room_type==1 || roomInfo.room_type==4">
                        <div class="bbbug_main_menu_icon">
                            <div @click="showSearchSongs">
                                <img :src="getStaticUrl('new/images/menubar_picksong.png')" title="点歌" />
                                <div>点歌</div>
                            </div>
                        </div>
                        <div class="bbbug_main_menu_icon">
                            <div @click="showPlaySongList">
                                <img :src="getStaticUrl('new/images/menubar_pickedsong.png')" title="已点歌曲列表" />
                                <div>已点</div>
                            </div>
                        </div>
                        <div class="bbbug_main_menu_icon">
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
                    <el-upload v-if="(roomInfo.room_type==1 || roomInfo.room_type==4) && userInfo.user_id>0"
                        :action="uploadMusicUrl" :show-file-list="false" :on-success="handleMusicUploadSuccess"
                        :before-upload="doUploadMusicBefore" :data="baseData">
                        <div class="bbbug_main_menu_icon">
                            <div>
                                <img :src="getStaticUrl('new/images/menubar_upload.png')" title="上传搜索不到的歌曲" />
                                <div>上传</div>
                            </div>
                        </div>
                    </el-upload>
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
                            <span class="bbbug_main_chat_invate"
                                onclick="window.open('https://qm.qq.com/cgi-bin/qm/qr?k=3yei1QB3MehVQoBWiDEMU0NmdMIdPwPD&jump_from=webapi');">QQ群</span>
                            <span title="复制邀请链接" class="bbbug_main_chat_invate"
                                :data-clipboard-text="copyData">邀请</span>
                            <span class="bbbug_main_chat_invate" title="显示当前房间微信小程序码" @click="showQrCode">小程序</span>
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
                            <div v-if="item.type=='text' || item.type=='img' || item.type=='link' || item.type=='jump' || item.type=='notice'"
                                class="bbbug_main_chat_item bbbug_main_chat_text"
                                :class="item.user.user_id==userInfo.user_id?'bbbug_main_chat_mine':''">
                                <div class="bbbug_main_chat_head">
                                    <el-dropdown trigger="click" @command="commandUserHead" :index="index">
                                        <img class="bbbug_main_chat_head_image xiaomi"
                                            :src="getStaticUrl(item.user.user_head)"
                                            :onerror="getStaticUrl('new/images/nohead.jpg')"
                                            @dblclick.stop="doTouch(item.user.user_id)" />
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
                                    :class="item.user.user_id<10000?'orangered':''"
                                    :title="item.user.user_id<10000?'骨灰级赞助用户':''">
                                    {{urldecode(item.user.user_name)}}
                                    <i class="iconfont icon-icon_certification_f user_icon"
                                        style="font-size:18px;color:#097AD8;" v-if="item.user.user_vip"
                                        :title="item.user.user_vip"></i>
                                    <span class="bbbug_main_chat_name_icon" v-if="item.user.user_icon==1"
                                        title="曾经的元老级管理员">元老</span>
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
                                        title="打开外部链接" @click="openNewWebPage(urldecode(item.link))"
                                        style="border-radius:10px">
                                        <div class="bbbug_main_chat_jump_name">
                                            {{urldecode(item.title)}}
                                        </div>
                                        <div class="bbbug_main_chat_jump_desc">{{urldecode(item.desc)||"没有读取到网站简介..."}}
                                        </div>
                                        <div class="bbbug_main_chat_jump_tips">{{urldecode(item.link)}}
                                        </div>
                                    </div>
                                    <!--房间公告-->
                                    <div class="bbbug_main_chat_content bbbug_main_chat_notice"
                                        v-if="item.type=='notice'" style="border-radius:10px">
                                        <div class="bbbug_main_chat_notice_title">房间公告</div>
                                        <div class="bbbug_main_chat_notice_content">
                                            {{urldecode(item.content)}}
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
                            <div v-if="item.type=='join'" class="bbbug_main_chat_system">
                                <span class="bbbug_main_chat_system_text">
                                    欢迎<span v-if="item.where">{{item.where}}的</span><span v-if="item.user">
                                        <font class="orangered" style="cursor: pointer;" title="点击查看资料"
                                            @click.stop="showUserPage(item.user.user_id)">
                                            {{urldecode(item.name)}} </font>
                                    </span><span v-if="!item.user"><span v-if="item.plat">{{item.plat}}用户</span><span
                                            v-if="!item.plat">临时用户</span></span>{{item.user?'回来!':''}}
                                </span>
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
                                :placeholder="(roomInfo && roomInfo.room_sendmsg==1 && roomInfo.room_user!=userInfo.user_id && !userInfo.user_admin)?'全员禁言中,你暂时无法发言...':'Wish you enjoy your bugs...'"
                                @keydown.13="sendMessage" @input="messageChanged" v-model="message"
                                :disabled="(roomInfo && roomInfo.room_sendmsg==1 && roomInfo.room_user!=userInfo.user_id && !userInfo.user_admin)?true:false"></textarea>
                        </div>
                        <button class="bbbug_main_chat_input_send" id="qqLoginBtn" @click.stop="sendMessage"
                            :class="isEnableSendMessage?'bbbug_main_chat_enable':''">发送(Enter)</button>
                        <el-tag class="bbbug_main_chat_input_quot" closable type="info"
                            v-if="atUserInfo && atUserInfo.message" @close="atUserInfo={user:{}};">
                            {{getQuotMessage(atUserInfo)}}
                        </el-tag>
                        <div class="bbbug_main_chat_input_lrc">{{lrcString}}</div>
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
                            class="orangered">
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
        <UploadMusic v-if="uploadSongForm"></UploadMusic>
        <div class="bbbug_locked" v-if="isLocked"
            @contextmenu.prevent="isLockedOnlyBg=!isLockedOnlyBg;saveLockBgConfig()">
            <div class="bg_back" :style="{backgroundImage:'url('+getStaticUrl(background)+')'}"></div>
            <div class="bg" v-if="songInfo" :style="{backgroundImage:'url('+getStaticUrl(songInfo.song.pic)+')'}"></div>
            <div v-show="!isLockedOnlyBg">
                <div class="logo xiaomi">
                    <img :src="getStaticUrl('new/images/logo.png')" />
                </div>

                <div class="copyright">所有音乐资源来源于第三方,请勿用于商业非法用途!</div>
                <div class="main">
                    <div class="pic"><img :src="getStaticUrl('new/images/logo.png')" /></div>
                    <div class="pic"><img
                            :src="songInfo ? getStaticUrl(songInfo.song.pic) : getStaticUrl('new/images/logo.png')" />
                    </div>
                    <div class="info" v-if="songInfo">
                        <div class="name">{{songInfo.song.name}} - {{songInfo.song.singer}}</div>
                        <div class="desc">[{{roomInfo.room_id}}]{{urldecode(roomInfo.room_name)}}
                            点歌人:{{urldecode(songInfo.user.user_name)}}</div>
                    </div>
                    <div class="info" v-if="!songInfo">
                        <div class="name">读碟中,请稍后...</div>
                        <div class="desc">[{{roomInfo.room_id}}]{{urldecode(roomInfo.room_name)}}</div>
                    </div>
                    <div class="controls" v-if="songInfo">
                        <i title="音量" @click="setEnableOrDisableVolume" class="iconfont volume_bar"
                            :class="audioVolume>0?'icon-changyongtubiao-xianxingdaochu-zhuanqu-39':'icon-changyongtubiao-xianxingdaochu-zhuanqu-40'"></i>
                        <i @click.stop="loveTheSong" title="收藏"
                            class="iconfont icon-changyongtubiao-xianxingdaochu-zhuanqu-15"
                            style="margin: 0px 180px;"></i>
                        <i @click.stop="passTheSong" title="切歌/不喜欢" class="iconfont icon-xiayige"></i>
                    </div>
                    <div class="progress">
                        <div :style="{width: audioPercent +'%'}"></div>
                    </div>
                    <div class="time">
                        <span class="now">{{audioTimeNow}}</span>
                        <span class="total">{{audioTimeTotal}}</span>
                    </div>
                    <div class="lrc">{{lrcString}}</div>
                </div>
            </div>
        </div>
        <audio :src="getStaticUrl('new/mp3/dingdong.mp3')" ref="noticePlayer"></audio>
        <audio :src="getStaticUrl('new/mp3/dong.m4a')" ref="audio_dong"></audio>
        <audio :src="getStaticUrl('new/mp3/ci.m4a')" ref="audio_ci"></audio>
        <audio :src="getStaticUrl('new/mp3/da.m4a')" ref="audio_da"></audio>
        <audio :src="nextAudioUrl" ref="preloadAudio" control1>
        </audio>
        <audio :src="audioUrl" ref="audio" control1 @timeupdate="audioTimeUpdate" @ended="audioEnded"
            @error="audioError" @loadedmetadata="audioLoaded" @canplay="canplay">
        </audio>
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
    import UploadMusic from './components/UploadMusic.vue';

    import Login from './components/Login.vue';

    export
        default {
            components: {
                UploadMusic,
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
                    uploadSongForm: false,
                    dialog: false,
                    audioUrl: "",
                    nextAudioUrl: "",
                    // 默认音乐loading图
                    audioImage: "new/images/loading.png",
                    // 歌曲加载失败重试
                    audioRetryTimes: 0,
                    uploadImageUrl: "",
                    uploadMusicUrl: "",
                    atUserInfo: false,
                    copyData: "",
                    userInfo: false,
                    roomInfo: false,
                    appLoading: false,
                    isLocked: false,
                    isLockedOnlyBg: false,
                    isEnableScroll: true,
                    isEnableNotification: true,
                    isEnableNoticePlayer: true,
                    isEnableTouchNotice: true,
                    isEnableAtNotification: true,
                    isEnableSendMessage: false,
                    isEmojiBoxShow: false,

                    messageList: [],
                    // 消息最大允许保留
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
                    // 默认音量
                    audioVolume: 50,
                    audioPercent: 0,
                    audioTimeNow: "",
                    audioTimeTotal: "",
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
                    // 默认背景图
                    background: "new/images/bg_dark.jpg",
                    emojiList: [],
                    imageList: [],
                    imageKeyword: "",
                }
            },
            created() {
                let that = this;
                this.global.guestUserInfo.user_head = this.getStaticUrl(this.global.guestUserInfo.user_head);
                this.isLockedOnlyBg = localStorage.getItem('isLockedOnlyBg') == 1 ? true : false;
            },
            mounted() {
                let that = this;
                // that.websocket.heartBeatTimer = setInterval(function () {
                //     if (that.websocket.isConnected && that.websocket.connection.readyState == 1) {
                //         that.websocket.connection.send('heartBeat');
                //     }
                // }, 10000);
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
                                console.log(e.keyCode);
                                if (that.isLocked) {
                                    switch (e.keyCode) {
                                        case 90:
                                        case 88:
                                        case 67:
                                        case 86:
                                        case 66:
                                        case 78:
                                        case 77:
                                            that.$refs.audio_dong.currentTime = 0;
                                            that.$refs.audio_dong.play();
                                            break;
                                        case 65:
                                        case 83:
                                        case 68:
                                        case 70:
                                        case 71:
                                        case 72:
                                        case 74:
                                        case 75:
                                        case 76:
                                            that.$refs.audio_ci.currentTime = 0;
                                            that.$refs.audio_ci.play();
                                            break;
                                        case 32:
                                            that.$refs.audio_da.currentTime = 0;
                                            that.$refs.audio_da.play();
                                            break;
                                        default:
                                    }
                                }
                        }
                    };

                    if (access_token) {
                        that.global.baseData.access_token = access_token;
                    } else {
                        that.global.baseData.access_token = that.global.guestUserInfo.access_token;
                    }
                    that.baseData = that.global.baseData;
                    that.uploadImageUrl = that.global.apiUrl + "/api/attach/uploadimage";
                    that.uploadMusicUrl = that.global.apiUrl + "/api/attach/uploadMusic";

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
                                    that.audioStartPlay();
                                }
                            });
                        }).catch(function () {
                            that.updateDarkModel(false);
                            that.getUserInfo();
                            that.callParentFunction('noticeClicked', 'success');
                            that.$nextTick(function () {
                                that.$refs.audio.volume = parseFloat(that.audioVolume / 100);
                                if (that.audioUrl) {
                                    that.audioStartPlay();
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
                                        that.audioStartPlay();
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
                /**
                 * @description: 保存播放器部分配置
                 * @param {null}
                 * @return {null}
                 */
                saveLockBgConfig() {
                    localStorage.setItem("isLockedOnlyBg", this.isLockedOnlyBg ? 1 : 0);
                },
                /**
                 * @description: 隐藏上传音乐窗口
                 * @param {null}
                 * @return {null}
                 */
                hideUploadMusicWindow() {
                    this.uploadSongForm = false;
                },
                /**
                 * @description: 初始化URL连接参数
                 * @param {function} 回调方法
                 * @return {null}
                 */
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
                                location.replace(location.origin);;
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
                            location.replace(location.origin);;
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
                            location.replace(location.origin);;
                        },
                        error(res) {
                            setTimeout(function () {
                                location.replace(location.origin);;
                            }, 3000);
                        }
                    });
                },
                /**
                 * @description: 获取URL参数
                 * @param {string} 参数名称
                 * @return {string|null} 
                 */
                getQueryString(name) {
                    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
                    var r = window.location.search.substr(1).match(reg);
                    if (r != null) return unescape(r[2]);
                    return null;
                },
                /**
                 * @description 显示当前房间的小程序码
                 * @param {null}
                 * @return {null}
                 */
                showQrCode() {
                    let that = this;
                    let roomAdminInfo = Object.assign({}, that.global.roomInfo.admin);
                    that.messageList.push({
                        type: "img",
                        content: that.global.apiUrl + '/api/weapp/qrcode?room_id=' + that.global.room_id,
                        resource: that.global.apiUrl + '/api/weapp/qrcode?room_id=' + that.global.room_id,
                        where: "channel",
                        at: roomAdminInfo,
                        message_id: 0,
                        time: parseInt(new Date().valueOf() / 1000),
                        user: roomAdminInfo
                    });
                    that.autoScroll();
                },
                /**
                 * @description: 游客登录
                 * @param {null}
                 * @return {null}
                 */
                loginGuest() {
                    localStorage.removeItem('access_token');
                    this.global.baseData.access_token = this.global.guestUserInfo.access_token;
                    this.global.userInfo = this.global.guestUserInfo;
                    this.hideAll();
                    this.getUserInfo();
                },
                /**
                 * @description: 显示登录窗口
                 * @param {null}
                 * @return {null}
                 */
                showLoginForm() {
                    this.userInfo = this.global.guestUserInfo;
                    this.hideAll();
                    this.dialog.Login = true;
                },
                /**
                 * @description: 设置暗黑模式
                 * @param {bool} 是否暗黑模式
                 * @return {null}
                 */
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
                /**
                 * @description: 读取本地配置
                 * @param {null}
                 * @return {null}
                 */
                loadConfig() {
                    this.isEnableNoticePlayer = localStorage.getItem('isEnableNoticePlayer') != 1 ? true : false;
                    this.isEnableNotification = localStorage.getItem('isEnableNotification') != 1 ? true : false;
                    this.isEnableTouchNotice = localStorage.getItem('isEnableTouchNotice') != 1 ? true : false;
                    this.isEnableAtNotification = localStorage.getItem('isEnableAtNotification') != 1 ? true : false;
                },
                /**
                 * @description: 显示右键菜单
                 * @param {event} 事件
                 * @param {object} 消息
                 * @return {null}
                 */
                openMenu(e, item) {
                    this.rightClickItem = item;
                    this.selectedMessage = item;
                    var x = e.pageX;
                    var y = e.pageY;

                    this.menuTop = y;
                    this.menuLeft = x;

                    this.menuVisible = true;
                },
                /**
                 * @description: 关闭右键菜单
                 * @param {null}
                 * @return {null}
                 */
                closeMenu() {
                    this.menuVisible = false;
                    this.selectedMessage = {
                        user: {}
                    };
                },
                getSongTime(long) {
                    long = parseInt(long);
                    if (long == 0 || long == NaN) {
                        return "00:00";
                    }
                    let string = "";
                    string += parseInt((long / 60)) < 10 ? ("0" + parseInt(long / 60)) : parseInt(long / 60);

                    string += ":";
                    string += (long % 60) < 10 ? (("0") + (long % 60)) : (long % 60)
                    return string;
                },
                /**
                 * @description: 格式化时间戳为友好时间
                 * @param {int} 秒时间戳
                 * @return {string} 友好时间
                 */
                friendlyTime: function (time) {
                    var now = parseInt(Date.parse(new Date()) / 1000);
                    var todayDate = new Date();
                    var todayTimeStamp = parseInt(Date.parse(new Date(todayDate.getFullYear() + "/" + (todayDate.getMonth() + 1) + '/' + todayDate.getDate() + " 00:00:00")) / 1000);
                    var date = new Date(time * 1000);
                    var y = date.getFullYear(),
                        m = date.getMonth() + 1,
                        d = date.getDate(),
                        h = date.getHours(),
                        i = date.getMinutes(),
                        s = date.getSeconds();
                    if (m < 10) { m = '0' + m; }
                    if (d < 10) { d = '0' + d; }
                    if (h < 10) { h = '0' + h; }
                    if (i < 10) { i = '0' + i; }
                    if (time > todayTimeStamp) {
                        return h + ":" + i;
                    } else if (now - time < 86400 * 365) {
                        return m + "-" + d + " " + h + ":" + i;
                    } else {
                        return y + "-" + m + "-" + d;
                    }
                },
                /**
                 * @description: 上传表情图片前校验
                 * @param {file} file
                 * @return {bool} 是否校验通过
                 */
                doUploadBefore(file) {
                    const isJPG = file.type === 'image/jpeg' || file.type === 'image/png' || file.type === 'image/gif';
                    const isLt2M = file.size / 1024 / 1024 < 2;

                    if (!isJPG) {
                        this.$message.error('发送图片只能是 JPG/PNG/GIF 格式!');
                        return false;
                    }
                    if (!isLt2M) {
                        this.$message.error('发送图片大小不能超过 2MB!');
                        return false;
                    }
                    return isJPG && isLt2M;
                },
                /**
                 * @description: 上传mp3前校验
                 * @param {file} file
                 * @return {bool} 是否校验通过
                 */
                doUploadMusicBefore(file) {
                    console.log(file.type);
                    const isMp3 = file.type.toLocaleLowerCase() === 'audio/mpeg' || file.type.toLocaleLowerCase() === 'audio/mp3';
                    const isLt8M = file.size / 1024 / 1024 < 8;

                    if (!isMp3) {
                        this.$message.error('音乐只允许上传mp3格式!');
                        return false;
                    }
                    if (!isLt8M) {
                        this.$message.error('上传音乐MP3不允许超过8MB!');
                        return false;
                    }
                    return isMp3 && isLt8M;
                },
                /**
                 * @description: 搜索表情
                 * @param {null}
                 * @return {null}
                 */
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
                /**
                 * @description: 获取剪切板文件
                 * @param {event} event
                 * @return {null}
                 */
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
                                        that.$alert('<img src="' + that.getStaticUrl(res.data.data.attach_path) + '" width="100%" height="100%"/><img src="' + that.getStaticUrl(res.data.data.attach_thumb) + '" width="0" height="0"/>', '是否确认发送这张图片？', {
                                            confirmButtonText: '确认发送',
                                            dangerouslyUseHTMLString: true,
                                        }).then(function () {
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
                                        }).catch(function () { });
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
                /**
                 * @description: 监听图片上传完成事件
                 * @param {obj} 服务器返回数据
                 * @param {file} 上传的文件
                 * @return {null}
                 */
                handleImageUploadSuccess(res, file) {
                    var that = this;
                    if (res.code == 200) {

                        that.$alert('<img src="' + that.getStaticUrl(res.data.attach_path) + '" width="100%" height="100%"/><img src="' + that.getStaticUrl(res.data.attach_thumb) + '" width="0" height="0"/>', '是否确认发送这张图片？', {
                            confirmButtonText: '确认发送',
                            dangerouslyUseHTMLString: true,
                        }).then(function () {
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
                        }).catch(function () { });
                    } else {
                        that.$message.error(res.msg);
                    }
                },
                /**
                 * @description: 监听MP3上传完成事件
                 * @param {obj} 服务器返回数据
                 * @param {file} 上传的文件
                 * @return {null}
                 */
                handleMusicUploadSuccess(res, file) {
                    let that = this;
                    if (res.code == 200) {
                        let musicPath = res.data.attach_path;
                        that.global.uploadMusicUrl = that.global.staticUrl + 'uploads/' + musicPath;
                        that.global.uploadMusicMid = res.data.attach_id;
                        that.uploadSongForm = true;
                    } else {
                        that.$message.error(res.msg);
                    }
                },
                /**
                 * @description: 新窗口打开URL
                 * @param {string} url
                 * @return {null}
                 */
                openNewWebPage(url) {
                    window.open(url);
                },
                /**
                 * @description: 与服务器同步时间戳
                 * @param {null}
                 * @return {null}
                 */
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
                /**
                 * @description: 静音和取消静音
                 * @param {null}
                 * @return {null}
                 */
                setEnableOrDisableVolume() {
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
                /**
                 * @description: 显示音量操作条
                 * @param {null}
                 * @return {null}
                 */
                showAudioVolumeBar() {
                    let that = this;
                    that.isVolumeBarShow = true;
                    that.hideVolumeBarAfter5seconds();
                },
                /**
                 * @description: 音量条数据改变
                 * @param {null}
                 * @return {null}
                 */
                audioVolumeChanged() {
                    let that = this;
                    that.$refs.audio.volume = parseFloat(that.audioVolume / 100);
                    localStorage.setItem('volume', that.audioVolume);
                    that.hideVolumeBarAfter5seconds();
                },
                /**
                 * @description: 5s后隐藏音量条
                 * @param {null}
                 * @return {null}
                 */
                hideVolumeBarAfter5seconds() {
                    let that = this;
                    clearTimeout(that.timerVolumeBar);
                    that.timerVolumeBar = setTimeout(function () {
                        that.isVolumeBarShow = false;
                    }, 5000);
                },
                /**
                 * @description: 播放进度变更事件
                 * @param {null}
                 * @return {null}
                 */
                audioTimeUpdate() {
                    let that = this;
                    if (that.songInfo && that.$refs.audio.duration > 0 && that.$refs.audio.duration != NaN) {
                        that.audioTimeNow = that.getSongTime(that.$refs.audio.currentTime);
                        that.audioTimeTotal = that.getSongTime(that.$refs.audio.duration);
                        that.audioPercent = parseInt(that.$refs.audio.currentTime / that.$refs.audio.duration * 10000) / 100;
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
                /**
                 * @description: Audio播放完毕
                 * @param {null}
                 * @return {null}
                 */
                audioEnded() {
                    let that = this;
                    that.audioResetImage();
                    if (that.roomInfo && that.roomInfo.room_type == 4 && that.roomInfo.room_playone) {
                        that.playMusic();
                    }
                },
                /**
                 * @description: Audio播放失败,可能需要替换为本地播放地址
                 * @param {null}
                 * @return {null}
                 */
                audioError() {
                    let that = this;
                    that.audioResetImage();
                    if (that.audioRetryTimes < 5) {
                        if (that.audioUrl) {
                            console.log("歌曲加载失败,正在准备重试");
                            setTimeout(function () {
                                that.$refs.audio.src = that.getStaticUrl("music/" + that.songInfo.song.mid + ".jpg");
                                that.audioRetryTimes++;
                            }, 500);
                        }
                    } else {
                        console.error("尝试了5次,还是加载不出来了...");
                    }
                },
                /**
                 * @description: 重置播放器图片为Loading
                 * @param {null}
                 * @return {null}
                 */
                audioResetImage() {
                    this.audioImage = this.getStaticUrl('new/images/loading.png');
                },
                /**
                 * @description: Audio加载成功
                 * @param {null}
                 * @return {null}
                 */
                audioLoaded() {
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
                            that.$refs.audio.currentTime = now < 0 ? 0 : now;
                            that.audioImage = decodeURIComponent(that.songInfo.user.user_head);
                            break;
                        default:
                    }
                    that.audioTimeNow = that.getSongTime(that.$refs.audio.currentTime);
                    that.audioTimeTotal = that.getSongTime(that.$refs.audio.duration);
                },
                /**
                 * @description: 可以播放事件 开始播放
                 * @param {null} 
                 * @return {null} 
                 */
                canplay() {
                    this.$refs.audio.play();
                },
                /**
                 * @description: 获取引用消息的标签
                 * @param {obj} 引用的消息
                 * @return {string} 显示字符串
                 */
                getQuotMessage(itemAt) {
                    if (!itemAt.message) {
                        return false;
                    }
                    switch (itemAt.message.type) {
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
                /**
                 * @description: 读取音乐歌词
                 * @param {null}
                 * @return {null}
                 */
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
                            that.musicLrcObj = res.data;
                        },
                    });
                },
                /**
                 * @description: 播放音乐
                 * @param {null}
                 * @return {null}
                 */
                playMusic() {
                    let that = this;
                    that.getMusicLrc();
                    that.audioRetryTimes = 0;
                    that.$nextTick(function () {
                        that.audioStartPlay();
                    });
                },
                /**
                 * @description: 播放系统提示音
                 * @param {null}
                 * @return {null}
                 */
                playSystemAudio() {
                    this.$refs.noticePlayer.play();
                },
                /**
                 * @description: 尝试播放 可能因为没加载完报错
                 * @param {null}
                 * @return {null}
                 */
                audioStartPlay() {
                    try {
                        this.$refs.audio.load();
                    } catch (e) { }
                },
                /**
                 * @description: 消息输入框输入变更
                 * @param {event} 输入框事件
                 * @return {null}
                 */
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
                /**
                 * @description: 消息列表滚动事件
                 * @param {event} 滚动事件
                 * @return {null}
                 */
                onMessageScroll(e) {
                    let that = this;
                    if (e.target.scrollHeight - e.target.scrollTop < e.target.clientHeight + 50 && !that.bbbug_loading) {
                        that.isEnableScroll = true;
                    } else {
                        that.isEnableScroll = false;
                    }
                },
                /**
                 * @description: 显示修改我的资料页面
                 * @param {null}
                 * @return {null}
                 */
                openMySetting() {
                    if (this.dialog.MySetting) {
                        this.hideAll();
                    } else {
                        this.hideAll();
                        this.dialog.MySetting = true;
                    }
                },
                /**
                 * @description: 显示我的歌曲列表
                 * @param {null}
                 * @return {null}
                 */
                showMySongList() {
                    if (this.dialog.MySongList) {
                        this.hideAll();
                    } else {
                        this.hideAll();
                        this.dialog.MySongList = true;
                    }
                },
                /**
                 * @description: 显示搜索歌曲页面
                 * @param {null}
                 * @return {null}
                 */
                showSearchSongs() {
                    if (this.dialog.SearchSongs) {
                        this.hideAll();
                    } else {
                        this.hideAll();
                        this.dialog.SearchSongs = true;
                    }
                },
                /**
                 * @description: 显示正在播放的列表
                 * @param {null}
                 * @return {null}
                 */
                showPlaySongList() {
                    if (this.dialog.PlayingSongList) {
                        this.hideAll();
                    } else {
                        this.hideAll();
                        this.dialog.PlayingSongList = true;
                    }
                },
                /**
                 * @description: 显示房间列表
                 * @param {null}
                 * @return {null}
                 */
                showRoomList() {
                    if (this.dialog.RoomList) {
                        this.hideAll();
                    } else {
                        this.hideAll();
                        this.dialog.RoomList = true;
                    }
                },
                /**
                 * @description: 显示系统设置
                 * @param {null}
                 * @return {null}
                 */
                showSystemSetting() {
                    if (this.dialog.SystemSetting) {
                        this.hideAll();
                    } else {
                        this.hideAll();
                        this.dialog.SystemSetting = true;
                    }
                },
                /**
                 * @description: 显示房间设置
                 * @param {null}
                 * @return {null}
                 */
                openRoomSetting() {
                    if (this.dialog.RoomSetting) {
                        this.hideAll();
                    } else {
                        this.hideAll();
                        this.dialog.RoomSetting = true;
                    }
                },
                /**
                 * @description: 隐藏所有窗口
                 * @param {null}
                 * @return {null}
                 */
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
                /**
                 * @description: 隐藏表情和歌曲卡片
                 * @param {null}
                 * @return {null}
                 */
                hideAllDialog() {
                    this.isEmojiBoxShow = false;
                    this.isSongPannelShow = false;
                    this.closeMenu();
                },
                /**
                 * @description: 显示歌曲卡片
                 * @param {null}
                 * @return {null}
                 */
                showSongPanel() {
                    this.closeMenu();
                    this.isEmojiBoxShow = false;
                    this.isSongPannelShow = !this.isSongPannelShow;
                },
                /**
                 * @description: 显示表情卡片
                 * @param {null}
                 * @return {null}
                 */
                showEmojiBox() {
                    this.closeMenu();
                    this.isSongPannelShow = false;
                    this.isEmojiBoxShow = !this.isEmojiBoxShow;
                    if (this.isEmojiBoxShow) {
                        this.imageList = this.emojiList;
                        this.loadingSearchImage = false;
                    }
                },
                /**
                 * @description: 获取图片宽度
                 * @param {string} 图片地址
                 * @return {int} 图片宽度
                 */
                getImageWidth(url) {
                    if (url.indexOf('/images/emoji/') > 0) {
                        return 60;
                    } else {
                        return 200;
                    }
                },
                /**
                 * @description: 发送图片
                 * @param {string} 图片URL
                 * @return {null}
                 */
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
                /**
                 * @description: 发送消息
                 * @param {event} 输入框事件
                 * @return {null}
                 */
                sendMessage(e) {
                    let that = this;
                    e.preventDefault();
                    if (that.message == '') {
                        return;
                    }

                    if (e.keyCode && e.keyCode == 13 && e.ctrlKey) {
                        that.hideAll();
                        that.global.songKeyword = that.message;
                        that.dialog.SearchSongs = true;
                        return;
                    }


                    let message = that.message;
                    that.message = '';
                    that.isEnableSendMessage = false;
                    if (that.messageList.length > that.historyMax) {
                        that.messageList.shift();
                    }
                    let _tempMessage = {
                        type: "text",
                        user: that.userInfo,
                        at: that.atUserInfo,
                        content: encodeURIComponent(message),
                        time: parseInt(new Date().valueOf() / 1000),
                        loading: 1,
                    };
                    that.messageList.push(_tempMessage);
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
                            that.isEnableScroll = true;
                            that.autoScroll();
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
                /**
                 * @description: 全局监听器
                 * @param {string} 执行方法名
                 * @return {null}
                 */
                AppController(data) {
                    eval("this." + data + "()");
                },
                /**
                 * @description: 隐藏全局Loading
                 * @param {null}
                 * @return {null}
                 */
                hideLoading() {
                    this.appLoading = false;
                },
                /**
                 * @description: 获取我的资料
                 * @param {null}
                 * @return {null}
                 */
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
                /**
                 * @description: 进入指定房间
                 * @param {int} 房间ID
                 * @return {null}
                 */
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
                /**
                 * @description: 切换到选择的房间 从缓存读取
                 * @param {null}
                 * @return {null}
                 */
                changeRoom() {
                    let that = this;
                    that.hideAll();
                    that.getRoomInfo();
                },
                /**
                 * @description: 获取房间聊天记录
                 * @param {null}
                 * @return {null}
                 */
                getRoomHistory() {
                    let that = this;
                    that.request({
                        url: "message/getMessageList",
                        data: {
                            room_id: that.global.room_id,
                            per_page: 20,
                        },
                        success(res) {
                            that.messageList = [];
                            for (let i = 0; i < res.data.length; i++) {
                                let _obj = false;
                                try {
                                    _obj = JSON.parse(res.data[i].message_content);
                                } catch (error) {
                                    continue;
                                }
                                if (_obj) {
                                    if (_obj.at) {
                                        _obj.content = '@' + _obj.at.user_name + " " + _obj.content;
                                    }
                                    _obj.time = res.data[i].message_createtime;
                                    _obj.isAtAll = false;
                                    if (_obj.type == 'text') {
                                        try {
                                            _obj.content = decodeURIComponent(decodeURIComponent(_obj.content));
                                        } catch (e) {
                                            _obj.content = decodeURIComponent(_obj.content);
                                        }
                                        _obj.isAtAll = _obj.content.indexOf('@全体') == 0 && (_obj.user.user_id == that.roomInfo.room_user || _obj.user.user_admin) ? true : false;
                                    }
                                    that.messageList.unshift(_obj);
                                }
                            }
                            if (that.messageList.length > that.historyMax) {
                                that.messageList.shift();
                            }
                            let roomAdminInfo = Object.assign({}, that.global.roomInfo.admin);
                            let _tempMessage = {
                                type: "notice",
                                content: encodeURIComponent(that.global.roomInfo.room_notice ? that.global.roomInfo.room_notice : ('欢迎来到' + that.global.roomInfo.room_name + '!')),
                                where: "channel",
                                at: null,
                                message_id: 0,
                                time: parseInt(new Date().valueOf() / 1000),
                                user: roomAdminInfo
                            };
                            that.messageList.push(_tempMessage);
                            if (that.roomInfo.room_id == 888 && that.userInfo.user_id < 0) {
                                that.messageList.push({
                                    type: "text",
                                    content: encodeURIComponent('临时用户你好，我们强烈建议你登录进来跟我们一起划水听歌聊天呀~'),
                                    where: "channel",
                                    at: roomAdminInfo,
                                    message_id: 0,
                                    time: parseInt(new Date().valueOf() / 1000),
                                    user: roomAdminInfo
                                });
                            }
                            that.autoScroll();
                        }
                    });
                },
                /**
                 * @description: 用户头像下拉点击事件
                 * @param {obj} 点击行
                 * @param {obj} 命令
                 * @return {obj} 返回对象
                 */
                beforeHandleUserCommand(row, command) {
                    return {
                        "row": row,
                        "command": command
                    }
                },
                /**
                 * @description: 输入框获取焦点
                 * @param {null}
                 * @return {null} 
                 */
                focusInput() {
                    const textarea = document.querySelector(".bbug_main_chat_input_message");
                    textarea.focus();
                },
                /**
                 * @description: at用户
                 * @param {null}
                 * @return {null} 
                 */
                atUser() {
                    if (this.global.atUserInfo) {
                        this.atUserInfo = this.global.atUserInfo;
                        this.message = '@' + decodeURIComponent(this.atUserInfo.user_name) + " " + this.message;
                        this.focusInput();
                    }
                },
                /**
                 * @description: 摸一摸指定用户
                 * @param {int} 用户ID
                 * @return {null} 
                 */
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
                /**
                 * @description: 撤回消息
                 * @param {obj} 消息体
                 * @return {null} 
                 */
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
                /**
                 * @description: 引用消息
                 * @param {obj} 消息体
                 * @return {null} 
                 */
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
                /**
                 * @description: 用户头像下拉点击回调
                 * @param {obj} 命令对象
                 * @return {null} 
                 */
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
                /**
                 * @description: 显示指定用户主页
                 * @param {int} 用户ID
                 * @return {null} 
                 */
                showUserPage(user_id) {
                    let that = this;
                    that.global.profileUserId = user_id;
                    that.hideAll();
                    that.$nextTick(function () {
                        that.hideAll();
                        that.dialog.Profile = true;
                    });
                },
                /**
                 * @description: 显示在线列表
                 * @param {null}
                 * @return {null} 
                 */
                showOnlineList() {
                    if (this.dialog.OnlineList) {
                        this.hideAll();
                    } else {
                        this.hideAll();
                        this.dialog.OnlineList = true;
                    }
                },
                /**
                 * @description: 自动滚动到底部
                 * @param {null}
                 * @return {null} 
                 */
                autoScroll() {
                    let that = this;
                    if (!that.isLocked) {
                        that.$nextTick(function () {
                            if (that.isEnableScroll) {
                                let ele = document.getElementById('bbbug_main_chat_history');
                                ele.scrollTop = ele.scrollHeight;
                            }
                        });
                    }
                },
                /**
                 * @description: 获取房间信息
                 * @param {bool} 是否重新连接(默认true)
                 * @return {null} 
                 */
                getRoomInfo(reConnect = true) {
                    let that = this;
                    if (reConnect) {
                        that.appLoading = true;
                    }
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
                            that.background = res.data.room_background || "new/images/bg_dark.jpg";
                            if (reConnect) {
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
                                    room_history.unshift();
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
                            } else {
                                let roomAdminInfo = Object.assign({}, that.global.roomInfo.admin);
                                let _tempMessage = {
                                    type: "notice",
                                    content: encodeURIComponent(that.global.roomInfo.room_notice ? that.global.roomInfo.room_notice : ('欢迎来到' + that.global.roomInfo.room_name + '!')),
                                    where: "channel",
                                    at: null,
                                    message_id: 0,
                                    time: parseInt(new Date().valueOf() / 1000),
                                    user: roomAdminInfo
                                };
                                that.messageList.push(_tempMessage);
                                that.autoScroll();
                            }
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
                /**
                 * @description: 更新剪切板数据
                 * @param {null}
                 * @return {null} 
                 */
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
                /**
                 * @description: 切掉当前播放的歌曲
                 * @param {null}
                 * @return {null} 
                 */
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
                /**
                 * @description: 收藏当前播放的歌曲
                 * @param {null}
                 * @return {null} 
                 */
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
                /**
                 * @description: 获取当前的websocket连接地址
                 * @param {null}
                 * @return {null} 
                 */
                getWebsocketUrl() {
                    let that = this;
                    that.request({
                        url: "room/getWebsocketUrl",
                        data: {
                            channel: that.global.room_id,
                            referer: document.referrer || false
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
                /**
                 * @description: 添加系统消息
                 * @param {string} 消息内容
                 * @param {string} 消息颜色(#999)
                 * @param {string} 背景色(transparent)
                 * @return {null}
                 */
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
                /**
                 * @description: Chrome通知
                 * @param {string} 标题
                 * @param {string} 内容
                 * @return {null}
                 */
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
                /**
                 * @description: 消息控制器
                 * @param {string} 收到的JSON数据包
                 * @return {null}
                 */
                messageController(data) {
                    let that = this;
                    try {
                        let obj = {};
                        //这里有点尴尬
                        try {
                            obj = JSON.parse(data);
                        } catch (e) {
                            return;
                        }
                        if (that.messageList.length > that.historyMax) {
                            that.messageList.shift();
                        }
                        obj.time = parseInt(new Date().valueOf() / 1000);
                        switch (obj.type) {
                            case 'preload':
                                that.nextAudioUrl = obj.url;
                                that.$nextTick(function () {
                                    that.$refs.noticePlayer.load();
                                });
                                break;
                            case 'touch':
                                that.addSystemMessage(that.urldecode(obj.user.user_name) + " 摸了摸 " + that.urldecode(obj.at.user_name) + obj.at.user_touchtip, '#999', '#eee');
                                if (obj.at) {
                                    if (obj.at.user_id == that.userInfo.user_id) {
                                        let notifyTitle = "摸一摸";
                                        let notifyContent = that.urldecode(obj.user.user_name) + " 摸了摸你" + that.urldecode(obj.at.user_touchtip);
                                        if (that.isEnableTouchNotice) {
                                            that.$notify({
                                                title: notifyTitle,
                                                message: notifyContent,
                                                duration: 10000,
                                                dangerouslyUseHTMLString: true
                                            });
                                        }
                                        if (that.isEnableNotification) {
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
                                try {
                                    obj.content = decodeURIComponent(decodeURIComponent(obj.content));
                                } catch (e) {
                                    obj.content = decodeURIComponent(obj.content);
                                }
                                if (obj.at) {
                                    try {
                                        obj.at.content = decodeURIComponent(decodeURIComponent(obj.content));
                                    } catch (e) {
                                        obj.content = decodeURIComponent(obj.content);
                                    }
                                }

                                obj.isAtAll = (obj.content).indexOf('@全体') == 0 && (obj.user.user_id == that.roomInfo.room_user || obj.user.user_admin) ? true : false;
                                if (obj.user.user_id == that.userInfo.user_id) {
                                    for (let i = that.messageList.length - 1; i >= 0; i--) {
                                        if (that.messageList[i].loading == 1) {
                                            that.messageList.splice(i, 1);
                                            break;
                                        }
                                    }
                                }
                                if (obj.user.user_id == 110) {
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
                                    if (that.isEnableNotification) {
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
                                        if (that.isEnableAtNotification) {
                                            that.$notify({
                                                title: notifyTitle,
                                                message: notifyContent,
                                                duration: 10000,
                                                dangerouslyUseHTMLString: true
                                            });
                                        }
                                        if (that.isEnableNotification) {
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
                            case 'join':
                                if (that.messageList.length > that.messageList.historyMax) {
                                    that.messageList.shift();
                                }
                                that.messageList.push(obj);
                                break;
                                // case 'join':
                                //     that.addSystemMessage(obj.content);
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
                                        if (that.isEnableNotification) {
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
                                that.addSystemMessage(that.urldecode(obj.user.user_name) + " 切掉了当前播放的歌曲 《" + obj.song.name + "》(" + obj.song.singer + ") ");

                                break;
                            case 'passGame':
                                that.addSystemMessage(that.urldecode(obj.user.user_name) + " PASS了当前的歌曲 《" + obj.song.name + "》(" + obj.song.singer + ") ");

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
                                    if (obj.song.mid < 0) {
                                        that.addSystemMessage('正在播放 ' + decodeURIComponent(obj.user.user_name) + ' 上传的歌曲《' + obj.song.name + '》(' + obj.song.singer + ')');
                                    }
                                    if (obj.user.user_id == that.userInfo.user_id) {
                                        let notifyTitle = "正在播放你点的歌";
                                        let notifyContent = "《" + obj.song.name + "》(" + obj.song.singer + ")";
                                        that.$notify({
                                            title: notifyTitle,
                                            message: notifyContent,
                                            duration: 5000,
                                            dangerouslyUseHTMLString: true
                                        });
                                        if (that.isEnableNotification) {
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
                                        if (that.isEnableNotification) {
                                            that.chromeNotify(notifyTitle, notifyContent);
                                        }
                                        if (that.isEnableNoticePlayer) {
                                            that.playSystemAudio()
                                        }
                                    }
                                }
                                break;
                            case 'online':
                                that.onlineList = obj.data || [];
                                break;
                            case 'roomUpdate':
                                that.getRoomInfo(obj.reConnect == 1 ? true : false);
                                break;
                            default:
                        }
                    } catch (error) {
                        console.log(error)
                    }
                    that.autoScroll();
                },

                /**
                 * @description: 连接websocket
                 * @param {null}
                 * @return {null}
                 */
                connectWebsocket() {
                    let that = this;
                    // console.log("connection...");
                    that.websocket.connection = new WebSocket(that.websocket.url);
                    that.websocket.connection.onopen = function (evt) {
                        // console.log("链接成功");
                        that.websocket.isConnected = true;
                        that.websocket.isForceStop = false;
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
                /**
                 * @description: 强制断开当前的websocket连接
                 * @param {null}
                 * @return {null}
                 */
                doForceCloseWebsocket() {
                    let that = this;
                    if (!that.websocket.isConnected) {
                        return true;
                    }
                    // console.log("wating...");
                    if (that.websocket.connection.readyState == 1) {
                        that.websocket.connection.send('bye');
                        that.websocket.connection.close();
                    }
                    setTimeout(function () {
                        return that.doForceCloseWebsocket();
                    }, 10);
                },
                /**
                 * @description: Websocket错误断开
                 * @param {null}
                 * @return {null}
                 */
                doWebsocketError() {
                    let that = this;
                    if (that.websocket.isForceStop) {
                        return;
                    }
                    // console.log("连接已断开，10s后将自动重连");
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
        line-height: 20px;
    }

    .bbbug_main_chat_name_icon {
        font-size: 12px;
        color: white;
        background-color: orangered;
        padding: 2px;
        border-radius: 3px;
        line-height: 12px;
        display: inline-block;
    }

    .bbbug_main_chat_mine .bbbug_main_chat_name {
        position: absolute;
        left: auto;
        right: 55px;
    }

    .bbbug_main_chat_context_menu {
        display: inline-block;
        overflow: hidden;
    }

    .bbbug_main_chat_content {
        background-color: #e5e5e5;
        padding: 8px 16px;
        border-radius: 10px;
        font-size: 14px;
        color: #666;
        max-width: 300px;
        margin-left: 60px;
        display: inline-block;
        word-break: break-all;
        word-wrap: break-word;
        position: relative;
    }

    .bbbug_main_chat_content::before {
        content: '';
        position: absolute;
        top: -6px;
        left: -13px;
        width: 15px;
        height: 15px;
        border-width: 0;
        border-style: solid;
        border-color: transparent;
        border-bottom-width: 12px;
        border-bottom-color: currentColor;
        border-radius: 0 0 0 15px;
        color: #E5E5E5;
    }

    .content_boy::before {
        color: #66CBFF;
    }

    .content_girl::before {
        color: #FE9898;
    }

    .content_at::before {
        color: #666;
    }

    .bbbug_main_chat_mine .bbbug_main_chat_content::before {
        content: '';
        position: absolute;
        right: -15px;
        top: -6px;
        left: calc(100% - 2px);
        width: 15px;
        height: 15px;
        border-width: 0;
        border-style: solid;
        border-color: transparent;
        border-bottom-width: 12px;
        border-bottom-color: currentColor;
        border-radius: 0 0 15px 0;
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
        margin-right: 60px;
        text-align: left;
    }


    .bbbug_main_chat_notice .bbbug_main_chat_notice_title {
        background-color: #666;
        color: white;
        margin: -8px -16px;
        padding: 5px 10px;
        font-size: 12px;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }

    .bbbug_main_chat_notice .bbbug_main_chat_notice_content {
        font-size: 14px;
        margin-top: 15px;
    }

    .bbbug_main_chat_notice::before {
        color: #666;
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
        border-top-right-radius: 10px;
        border-bottom-right-radius: 10px;
        overflow: hidden;
    }

    .bbbug_main_chat_input {
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        height: 100px;
        border-top: 1px solid #eee;
    }

    .bbbug_main_chat_input_form {
        padding: 10px;
        position: absolute;
        left: 10px;
        right: 10px;
        top: 10px;
        bottom: 10px;
    }

    .bbug_main_chat_input_message {
        width: 100%;
        height: 50px;
        outline: none;
        resize: none;
        background-color: transparent;
        position: absolute;
        left: 0;
        right: 0;
        top: 0;
        bottom: 0;
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
        z-index: 0;
    }

    .bbbug_main_chat_system {
        text-align: center;
        margin: 5px 10%;
        margin-top: 10px;
        overflow: hidden;
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
        padding-right: 20px;
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
        font-size: 14px;
        text-decoration: none;
        color: rgba(255, 255, 255, 1);
        text-shadow: 0px 0px 5px rgba(0, 0, 0, 0.5);
    }

    .bbbug_link a:hover {
        color: #fff;
        text-decoration: underline;
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


    .bbbug_main_chat_emojis_input {
        margin-bottom: 10px;
    }

    .bbbug_locked {
        display: -ms-flexbox;
        display: flex;
        -ms-flex-align: center;
        align-items: center;
        -ms-flex-pack: center;
        justify-content: center;
        position: fixed;
        left: 0;
        right: 0;
        top: 0;
        bottom: 0;
    }

    .bbbug_locked .main {
        width: 1000px;
        height: 500px;
        backdrop-filter: blur(0px);
        text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.3);
    }

    .bbbug_locked .bg_back {
        filter: blur(50px);
        position: fixed;
        left: -100px;
        right: -100px;
        top: -100px;
        bottom: -100px;
        background: #000 url(https://bbbug.hamm.cn/new/images/bg_dark.jpg) center no-repeat;
        background-size: cover;
    }

    .bbbug_locked .bg {
        filter: blur(50px);
        position: fixed;
        left: -100px;
        right: -100px;
        top: -100px;
        bottom: -100px;
        background: transparent url(https://bbbug.hamm.cn/new/images/bg_dark.jpg) center no-repeat;
        background-size: cover;
    }

    .bbbug_locked {
        color: white;
        z-index: 100;
    }

    .bbbug_locked .pic {
        width: 360px;
        height: 360px;
        position: absolute;
        left: 0px;
        top: 50px;
        border-radius: 20px;
        overflow: hidden;
    }

    .bbbug_locked .pic img {
        width: 100%;
        height: 100%;
    }

    .bbbug_locked .info {
        position: absolute;
        left: 400px;
        top: 50px;
        width: 560px;
    }

    .bbbug_locked .name {
        font-size: 36px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .bbbug_locked .desc {
        font-size: 20px;
        margin-top: 10px;
        color: rgba(255, 255, 255, 0.5);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .bbbug_locked .controls {
        position: absolute;
        left: 400px;
        top: 200px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .bbbug_locked .controls .iconfont {
        display: inline-block;
        font-size: 64px;
        cursor: pointer;
    }

    .bbbug_locked .progress {
        background-color: rgba(255, 255, 255, 0.1);
        position: absolute;
        left: 400px;
        top: 330px;
        height: 5px;
        border-radius: 20px;
        width: 560px;
    }

    .bbbug_locked .progress div {
        background: rgba(255, 255, 255, 0.5);
        width: 20%;
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        border-radius: 20px;
    }

    .bbbug_locked .time {
        position: absolute;
        left: 400px;
        top: 340px;
        color: rgba(255, 255, 255, 0.8);
        width: 560px;
    }

    .bbbug_locked .time .now {
        position: absolute;
        left: 0;
        top: 0;
    }

    .bbbug_locked .time .total {
        position: absolute;
        right: 0;
        top: 0;
    }

    .bbbug_locked .logo {
        position: absolute;
        left: 20px;
        top: 20px;
    }

    .bbbug_locked .logo img {
        width: 40px;
        height: 40px;
        vertical-align: middle;
        border-radius: 10px;
    }

    .bbbug_locked .copyright {
        position: fixed;
        right: 20px;
        bottom: 20px;
        font-size: 12px;
        color: rgba(255, 255, 255, 0.8);
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
    }

    .bbbug_locked .lrc {
        font-size: 20px;
        color: rgba(255, 255, 255, 0.8);
        position: absolute;
        left: 400px;
        top: 380px;
        width: 560px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>