<template>
    <div id="RoomList">
        <div class="bbbug_main_right">
            <div class="bbbug_main_right_room">
                <div class="bbbug_main_right_room_title">热门房间列表
                    <router-link to="/create_room" class="bbbug_main_right_song_right" v-if="!userInfo.myRoom">创建房间
                    </router-link>
                    <div class="bbbug_main_right_room_right" @click="joinMyRoom" v-if="userInfo.myRoom">我的房间</div>
                    <el-input style="margin-top:20px;" placeholder="输入房间ID" size="small" class="input-with-select"
                        prefix-icon="el-icon-search" v-model="room_id" @keydown.13.native="joinRoom(room_id)">
                        <el-button slot="append" @click="joinRoom(room_id)">进入</el-button>
                    </el-input>
                </div>
                <div class="bbbug_main_right_room_list" v-loading="bbbug_loading" v-if="list.length>0">
                    <div class="bbbug_scroll">
                        <div class="bbbug_main_right_room_item" v-for="(item,index) in list"
                            @click="joinRoom(item.room_id)">
                            <div class="bbbug_main_right_room_name">
                                <i class="bbbug_main_room_icon iconfont icon-changyongtubiao-mianxing-61"
                                    v-if="item.room_type==1"></i>
                                <i title="房主播放器" class="iconfont bbbug_main_room_icon icon-icon_voice"
                                    v-if="item.room_type==4"></i></span>
                                {{item.room_name}}
                            </div>
                            <div class="bbbug_main_right_room_id"><i v-if="item.room_public"
                                    class="room_lock iconfont icon-lock_fill"></i>ID:{{item.room_id}}</div>
                            <div class="bbbug_main_right_room_info">
                                <img class="bbbug_main_right_room_user_head"
                                    :src="item ? http2https(item.user_head) : '//cdn.bbbug.com/new/images/nohead.jpg'"
                                    onerror="this.src='//cdn.bbbug.com/new/images/nohead.jpg'" />
                                <div class="bbbug_main_right_room_user">
                                    <div class="bbbug_main_right_room_user_nick"><span class="bbbug_main_room_online"
                                            v-if="item.room_online>0">({{item.room_online}})
                                        </span>{{urldecode(item.user_name)}}</div>
                                    <div class="bbbug_main_right_room_desc">{{item.room_notice || '实在是懒到连房间公告都不填写...'}}
                                    </div>
                                </div>
                            </div>
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
                    room_id: ""
                }
            },
            created() {
                this.userInfo = this.global.userInfo;
                this.getList();
            },
            methods: {
                urldecode(str) {
                    return decodeURIComponent(str);
                },
                joinRoom(room_id) {
                    let that = this;
                    if (room_id) {
                        localStorage.setItem('room_change_id', room_id);
                        that.$emit('App', 'changeRoom');
                    }
                },
                joinMyRoom() {
                    let that = this;
                    let room_id = that.userInfo.myRoom.room_id;
                    localStorage.setItem('room_change_id', room_id);
                    that.$emit('App', 'changeRoom');
                },
                getList() {
                    let that = this;
                    if (that.bbbug_loading) {
                        return;
                    }
                    that.bbbug_loading = true;
                    that.request({
                        url: "room/hotRooms",
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
    .room_lock {
        vertical-align: middle;
        color: orangered;
    }
</style>