<template>
    <div id="OnlineList">
        <div class="bbbug_main_right bbbug_main_right_online_box">
            <div class="bbbug_main_right_online">
                <div class="bbbug_main_right_online_title">房间在线用户
                </div>
                <div class="bbbug_main_right_online_list" v-loading="bbbug_loading">
                    <div class="bbbug_scroll" v-if="list.length>0">
                        <div class="bbbug_main_right_online_item" v-for="(item,index) in list"
                            :style="{filter:item.user_shutdown?'grayscale(1)':'none'}" :title="getStatus(item)">
                            <div class="bbbug_main_right_online_info">
                                <div class="bbbug_main_right_online_user_head">
                                    <el-dropdown trigger="click" @command="commandUserHead" :index="index">
                                        <img class="bbbug_main_right_online_user_head_image"
                                            :src="getStaticUrl(item.user_head)"
                                            :onerror="getStaticUrl('new/images/nohead.jpg')"
                                            @dblclick="doTouch(item.user_id)" />
                                        <el-dropdown-menu slot="dropdown">
                                            <el-dropdown-item :command="beforeHandleUserCommand(item, 'at')">
                                                @Ta
                                            </el-dropdown-item>
                                            <!--
                                            <el-dropdown-item :command="beforeHandleUserCommand(item, 'touch')"
                                                v-if="item.user_id!=userInfo.user_id">
                                                摸一摸
                                            </el-dropdown-item>
                                            -->
                                            <el-dropdown-item :command="beforeHandleUserCommand(item, 'sendSong')"
                                                v-if="item.user_id!=userInfo.user_id">
                                                送歌给Ta
                                            </el-dropdown-item>
                                            <el-dropdown-item divided
                                                :command="beforeHandleUserCommand(item, 'removeBan')"
                                                v-if="(userInfo.user_admin||userInfo.user_id==roomInfo.room_user) && (item.user_shutdown || item.user_songdown)">
                                                解除所有限制
                                            </el-dropdown-item>
                                            <el-dropdown-item :command="beforeHandleUserCommand(item, 'shutdown')"
                                                v-if="(userInfo.user_admin||userInfo.user_id==roomInfo.room_user) && !item.user_shutdown">
                                                禁止发言
                                            </el-dropdown-item>
                                            <el-dropdown-item :command="beforeHandleUserCommand(item, 'songdown')"
                                                v-if="(userInfo.user_admin||userInfo.user_id==roomInfo.room_user) && !item.user_songdown">
                                                禁止点歌
                                            </el-dropdown-item>
                                            <el-dropdown-item :command="beforeHandleUserCommand(item, 'guestctrl')"
                                                v-if="(userInfo.user_admin||userInfo.user_id==roomInfo.room_user) && roomInfo.room_sendmsg==2 && !item.user_admin">
                                                <span v-if="item.user_guest">取消嘉宾身份</span>
                                                <span v-if="!item.user_guest">设置为嘉宾</span>
                                            </el-dropdown-item>
                                            <el-dropdown-item divided
                                                :command="beforeHandleUserCommand(item, 'profile')">
                                                查看主页
                                            </el-dropdown-item>
                                        </el-dropdown-menu>
                                    </el-dropdown>
                                </div>
                                <div class="bbbug_main_right_online_user">
                                    <div class="bbbug_main_right_online_user_nick"
                                        :style="{color:item.user_id<10000?'orangered':item.user_shutdown?'#aaa':'#333'}"
                                        :title="item.user_id<10000?'骨灰级赞助用户':''">
                                        <i class="iconfont icon-icon_certification_f user_icon"
                                            style="font-size:18px;color:#097AD8;" v-if="item.user_vip"
                                            :title="item.user_vip"></i>
                                        {{urldecode(item.user_name)}}
                                    </div>
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


                            <div class="bbbug_main_right_online_user_badge_guest" v-if="item.user_guest">宾</div>
                        </div>
                    </div>
                    <div class="bbbug_tips" v-if="list.length==0">暂无在线用户</div>
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
                    this.$parent.hideAll();
                }
            },
            methods: {
                /**
                 * @description: 获取用户当前状态
                 * @param {obj} 用户信息
                 * @return {string} 状态字符串
                 */
                getStatus(item) {
                    if (item.user_shutdown && item.user_songdown) {
                        return '禁言&禁歌';
                    } else if (item.user_shutdown) {
                        return '禁言';
                    } else if (item.user_songdown) {
                        return '禁歌';
                    } else {
                        return '';
                    }
                },
                /**
                 * @description: 头像下拉点击事件
                 * @param {obj} 点击的行 
                 * @return {string} 回调命令
                 */
                beforeHandleUserCommand(row, command) {
                    return {
                        "row": row,
                        "command": command
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
                 * @description: 用户下拉点击事件回调
                 * @param {obj} 回调数据
                 * @return {null}
                 */
                commandUserHead(cmd) {
                    let that = this;
                    switch (cmd.command) {
                        case 'at':
                            that.global.atUserInfo = {
                                user_id: cmd.row.user_id,
                                user_name: cmd.row.user_name
                            };
                            that.$parent.atUser();
                            that.$parent.hideAll();
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
                                },
                                success(res) {
                                    that.$message.success(res.msg);
                                    that.getList();
                                }
                            });
                            break;
                        case 'guestctrl':
                            that.request({
                                url: "user/guestctrl",
                                data: {
                                    user_id: cmd.row.user_id,
                                    room_id: that.global.room_id
                                },
                                success(res) {
                                    that.$message.success(res.msg);
                                    that.getList();
                                }
                            });
                            break;
                        case 'songdown':
                            that.request({
                                url: "user/songdown",
                                data: {
                                    user_id: cmd.row.user_id,
                                    room_id: that.global.room_id
                                },
                                success(res) {
                                    that.$message.success(res.msg);
                                    that.getList();
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
                                    that.getList();
                                }
                            });
                            break;
                        case 'profile':
                            that.global.profileUserId = cmd.row.user_id;
                            that.$nextTick(function () {
                                that.$parent.hideAll();
                                that.$parent.dialog.Profile = true;
                            });
                            break;
                        case 'sendSong':
                            that.global.atSongUserInfo = cmd.row;
                            that.$parent.hideAll();
                            that.$parent.dialog.SearchSongs = true;
                            break;
                        default:
                            that.$message.error('即将上线，敬请期待');
                    }
                },
                /**
                 * @description: 获取在线用户列表
                 * @param {null} 
                 * @return {null}
                 */
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