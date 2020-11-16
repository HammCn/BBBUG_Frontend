<template>
    <div id="OnlineList">
        <div class="bbbug_main_right bbbug_main_right_online_box">
            <div class="bbbug_main_right_online">
                <div class="bbbug_main_right_online_title">房间在线用户
                </div>
                <div class="bbbug_main_right_online_list" v-loading="bbbug_loading" v-if="list.length>0">
                    <div class="bbbug_scroll">
                        <div class="bbbug_main_right_online_item" v-for="(item,index) in list">
                            <div class="bbbug_main_right_online_info">
                                <div class="bbbug_main_right_online_user_head">
                                    <el-dropdown trigger="click" @command="commandUserHead" :index="index">
                                        <img class="bbbug_main_right_online_user_head_image" :src="http2https(item.user_head)"
                                            onerror="this.src='//cdn.bbbug.com/new/images/nohead.jpg'"
                                            @dblclick="doTouch(item.user_id)" />
                                        <el-dropdown-menu slot="dropdown">
                                            <el-dropdown-item :command="beforeHandleUserCommand(item, 'at')"
                                                v-if="item.user_id!=userInfo.user_id">
                                                @Ta
                                            </el-dropdown-item>
                                            <el-dropdown-item :command="beforeHandleUserCommand(item, 'touch')"
                                                v-if="item.user_id!=userInfo.user_id">
                                                摸摸
                                            </el-dropdown-item>
                                            <el-dropdown-item :command="beforeHandleUserCommand(item, 'sendSong')"
                                                v-if="item.user_id!=userInfo.user_id">
                                                送歌
                                            </el-dropdown-item>
                                            <el-dropdown-item :command="beforeHandleUserCommand(item, 'removeBan')"
                                                v-if="userInfo.user_admin||userInfo.user_id==roomInfo.room_user">
                                                解禁
                                            </el-dropdown-item>
                                            <el-dropdown-item :command="beforeHandleUserCommand(item, 'shutdown')"
                                                v-if="userInfo.user_admin||userInfo.user_id==roomInfo.room_user">
                                                禁言
                                            </el-dropdown-item>
                                            <el-dropdown-item :command="beforeHandleUserCommand(item, 'songdown')"
                                                v-if="userInfo.user_admin||userInfo.user_id==roomInfo.room_user">
                                                禁歌
                                            </el-dropdown-item>
                                            <el-dropdown-item :command="beforeHandleUserCommand(item, 'profile')">
                                                主页
                                            </el-dropdown-item>
                                        </el-dropdown-menu>
                                    </el-dropdown>
                                </div>
                                <div class="bbbug_main_right_online_user">
                                    <div class="bbbug_main_right_online_user_nick">
                                        <i class="iconfont icon-icon_certification_f user_icon"
                                            style="font-size:18px;color:#097AD8;" v-if="item.user_vip"
                                            :title="item.user_vip"></i>
                                        <i class="iconfont icon-weixin user_icon" style="font-size:16px;color:#666;"
                                            v-if="item.user_icon" title="使用过微信小程序即可点亮"></i>
                                        {{urldecode(item.user_name)}}</div>
                                    <div class="bbbug_main_right_online_desc">
                                        <i class="iconfont user_icon user_female icon-xingbie-nv" title="女生"
                                            v-if="item.user_sex==0"></i>
                                        <i class="iconfont user_icon user_male icon-xingbie-nan" title="男生"
                                            v-if="item.user_sex==1"></i>
                                        <span>{{item.user_remark || '实在是懒到连签名都懒得签...'}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="bbbug_main_right_online_user_badge_admin" v-if="item.user_admin">管</div>
                            <div class="bbbug_main_right_online_user_badge"
                                v-if="!item.user_admin && item.user_id == roomInfo.room_user">房</div>
                        </div>
                    </div>
                    <div class="bbbug_tips" v-if="list.length==0">没有查到房间</div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export
        default {
            data() {
                return {
                    bbbug_loading: false,
                    list: [],
                    userInfo: null,
                    room_id: "",
                    roomInfo: false
                }
            },
            created() {
                if (this.global.userInfo && this.global.roomInfo) {
                    this.userInfo = this.global.userInfo;
                    this.roomInfo = this.global.roomInfo;
                    this.getList();
                } else {
                    this.$router.push('/');
                }
            },
            methods: {
                beforeHandleUserCommand(row, command) {
                    return {
                        "row": row,
                        "command": command
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
                commandUserHead(cmd) {
                    let that = this;
                    switch (cmd.command) {
                        case 'at':
                            that.global.atUserInfo = {
                                user_id: cmd.row.user_id,
                                user_name: cmd.row.user_name
                            };
                            that.$emit('App', 'atUser');
                            that.$router.push('/');
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
                            that.$nextTick(function () {
                                that.$router.push('/profile');
                            });
                            break;
                        case 'sendSong':
                            that.global.atSongUserInfo = cmd.row;
                            that.$router.push('/search_songs');
                            break;
                        default:
                            that.$message.error('即将上线，敬请期待');
                    }
                },
                getList() {
                    let that = this;
                    if (that.bbbug_loading) {
                        return;
                    }
                    that.bbbug_loading = true;
                    that.request({
                        url: "user/online",
                        data: {
                            room_id: that.global.room_id
                        },
                        success(res) {
                            that.bbbug_loading = false;
                            that.list = res.data;
                        },
                        error(res) {
                            that.bbbug_loading = false;
                        }
                    });
                }
            },
        }
</script>
<style>

</style>
</style>