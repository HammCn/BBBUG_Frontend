<template>
    <div id="RoomSetting">
        <div class="bbbug_main_right">
            <div class="bbbug_main_right_room">
                <div class="bbbut_main_profile_box" v-loading="loading">
                    <div class="bbbugbbbug_main_profile_user_badge_admin" v-if="userInfo.user_admin">管</div>
                    <div class="bbbugbbbug_main_profile_user_badge"
                        v-if="!userInfo.user_admin && userInfo.user_id == roomInfo.room_user">房</div>
                    <div class="bbbug_main_profile_head">
                        <img :src="userInfo.user_head" onerror="this.src='//cdn.bbbug.com/images/nohead.jpg'"
                            style="border-radius: 100%;width:80px;height:80px;" />
                    </div>
                    <div class="bbbug_main_profile_user">
                        <div class="bbbug_main_profile_user_icon">
                            <span class="bbbug_main_profile_user_id">ID:{{userInfo.user_id}}</span>
                            <i class="iconfont user_icon user_female icon-xingbie-nv" title="女生"
                                v-if="userInfo.user_sex==0"></i>
                            <i class="iconfont user_icon user_male icon-xingbie-nan" title="男生"
                                v-if="userInfo.user_sex==1"></i>
                        </div>
                        <div class="bbbug_main_profile_user_name"><i :title="userInfo.user_vip" v-if="userInfo.user_vip"
                                class="iconfont icon-icon_certification_f user_icon"
                                style="font-size: 18px; color: rgb(9, 122, 216);"></i> <i title="1024程序员节彩蛋"
                                class="iconfont icon-github user_icon"
                                style="font-size: 16px; color: rgb(102, 102, 102);" v-if="userInfo.user_icon"></i>
                            {{urldecode(userInfo.user_name)}}</div>
                    </div>
                    <div class="bbbug_main_profile_song_title">Ta最近在听的歌</div>
                    <div class="bbbug_main_profile_song" v-if="songList.length>0">
                        <div class="bbbug_main_profile_song_item" v-for="(item,index) in songList">
                            <div class="bbbug_main_profile_song_name">{{item.name}}</div>
                            <div class="bbbug_main_profile_song_singer">{{item.singer}}</div>
                            <div class="bbbug_main_profile_song_played">{{item.played}}次</div>
                        </div>
                    </div>
                    <div class="bbbug_main_profile_song_tips" v-if="songList.length==0">好尴尬，Ta还没有点过歌...</div>
                    <div class="bbbug_main_profile_button" @click="enterHisRoom">去Ta房间看看</div>
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
                    userInfo: {},
                    songList: [],
                    roomInfo: {},
                    loading: true,
                }
            },
            created() {
                if (!this.global.roomInfo) {
                    this.$router.push('/');
                    return;
                }
                this.roomInfo = this.global.roomInfo;
                this.getUserProfile();
                this.getSongList();
            },
            methods: {
                getUserProfile() {
                    let that = this;
                    that.request({
                        url: "user/getuserinfo",
                        data: {
                            user_id: that.global.profileUserId
                        },
                        success(res) {
                            that.userInfo = res.data;
                        }
                    });
                },
                getSongList() {
                    let that = this;
                    that.request({
                        url: "song/getusersongs",
                        data: {
                            user_id: that.global.profileUserId
                        },
                        success(res) {
                            that.songList = res.data;
                            that.loading = false;
                        }
                    });
                },
                enterHisRoom() {
                    let that = this;
                    if (that.userInfo.myRoom) {
                        let room_id = that.userInfo.myRoom.room_id;
                        localStorage.setItem('room_change_id', room_id);
                        that.$emit('App', 'changeRoom');
                    } else {
                        that.$message.error("Ta当前没有创建自己的音乐房间");
                    }
                },
            },
        }
</script>
<style>
    .bbbugbbbug_main_profile_user_badge {
        background-color: #ddd;
        color: #666;
        transform: rotate(45deg);
        position: absolute;
        right: -25px;
        top: -25px;
        height: 50px;
        width: 50px;
        text-align: center;
        line-height: 78px;
        font-size: 12px;
    }

    .bbbugbbbug_main_profile_user_badge_admin {
        background-color: #666;
        color: #fff;
        transform: rotate(45deg);
        position: absolute;
        right: -25px;
        top: -25px;
        height: 50px;
        width: 50px;
        text-align: center;
        line-height: 78px;
        font-size: 12px;
    }

    .bbbug_my_setting__form {
        background-color: white;
        padding: 20px;
        padding-left: 0;
        position: absolute;
        left: 0;
        right: 0;
        bottom: 60px;
        top: 55px;
        overflow: hidden;
        overflow-y: auto;
    }

    .bbbug_main_profile_song_tips {
        background: #fff;
        color: #aaa;
        font-size: 12px;
        position: absolute;
        left: 0;
        right: 0;
        bottom: 50px;
        top: 230px;
        justify-content: center;
        align-items: center;
        display: flex;
    }

    .bbbug_main_profile_button {
        position: absolute;
        right: 10px;
        bottom: 10px;
        text-align: right;
        background-color: #666;
        color: white;
        padding: 8px 16px;
        border-radius: 3px;
        border: none;
        outline: none;
        cursor: pointer;
        font-size: 12px;
    }

    .bbbug_main_profile_button:active {
        background-color: #333;
    }

    .bbbug_main_profile_head {
        margin-top: 50px;
    }


    .bbbug_main_profile_user_name {
        font-size: 16px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        margin: 0px 20px;
        vertical-align: middle;
        margin-top: 10px;
        line-height: 20px;
    }

    .bbbug_main_profile_song_item {
        position: relative;
        border-bottom: 1px solid #f5f5f5;
        text-align: left;
        padding: 10px;
    }

    .bbbug_main_profile_song_name {
        color: #333;
        font-size: 14px;
    }

    .bbbut_main_profile_box {
        background-color: #f5f5f5;
        position: absolute;
        left: 0;
        right: 0;
        top: 0;
        bottom: 0;
    }

    .bbbug_main_profile_song {
        overflow: hidden;
        overflow-y: auto;
        position: absolute;
        background: white;
        left: 0;
        right: 0;
        top: 230px;
        bottom: 50px;
        border-bottom: 1px solid #eee;
    }

    .bbbug_main_profile_song_singer {
        font-size: 12px;
        color: #999;
        margin-top: 10px;
    }

    .bbbug_main_profile_song_played {
        position: absolute;
        right: 10px;
        top: 20px;
        font-size: 12px;
        color: orangered;
    }

    .bbbug_main_profile_user_icon {
        font-size: 12px;
        color: #999;
        vertical-align: middle;
    }

    .bbbug_main_profile_user_icon * {
        vertical-align: middle;
    }

    .bbbug_main_profile_user_id {
        background-color: #999;
        color: white;
        border-radius: 3px;
        padding: 0px 3px;
        margin-right: 5px;
    }

    .bbbug_main_profile_song_title {
        text-align: left;
        font-size: 14px;
        color: #999;
        background-color: #f5f5f5;
        margin-top: 15px;
        padding: 10px;
    }
</style>