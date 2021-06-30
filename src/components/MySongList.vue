<template>
    <div id="MySongList">
        <div class="bbbug_main_right">
            <div class="bbbug_main_right_song">
                <div class="bbbug_main_right_song_title">我点过的歌</div>
                <div class="bbbug_main_right_song_list" v-loading="bbbug_loading">
                    <div class="bbbug_scroll" v-if="list.length>0" @scroll="onScroll">
                        <div class="bbbug_main_right_song_item" v-for="(item,index) in list" v-loading="item.loading">
                            <div class="bbug_main_right_song_pic">
                                <img :src="getStaticUrl(item.pic)" class="xiaomi" 
                                    :onerror="getStaticUrl('new/images/nohead.jpg')" />
                            </div>
                            <div class="bbbug_main_right_song_name">{{item.name}}
                            </div>
                            <div class="bbbug_main_right_song_singer">
                                <span class="bbbug_main_right_song_played">({{item.played}})</span>
                                {{item.singer}}
                            </div>
                            <div class="bbbug_main_right_song_controll">
                                <button class="bbbug_main_right_song_button bbbug_main_right_song_delete"
                                    @click="removeSong(index)">删除</button>
                                <button
                                    v-if="roomInfo.room_type==1 || roomInfo.room_type==4 && roomInfo.room_user!=userInfo.user_id"
                                    class="bbbug_main_right_song_button bbbug_main_right_song_add"
                                    @click="addSong(index)">点歌</button>
                                <button v-if="roomInfo.room_type==4 && roomInfo.room_user==userInfo.user_id"
                                    class="bbbug_main_right_song_button bbbug_main_right_song_add"
                                    @click="playSong(index)">播放</button>
                            </div>
                        </div>
                    </div>
                    <div class="bbbug_tips" v-if="list.length==0">好尴尬，你还没点过歌吗？？？</div>
                    <div v-if="atSongUserInfo" class="bbbug_search_song_user">
                        <div class="bbbug_search_song_user_box">
                            <img class="bbbug_search_song_head" :src="atSongUserInfo.user_head"
                                :onerror="getStaticUrl('new/images/nohead.jpg')" />
                            <div class="bbbug_search_song_name">{{urldecode(atSongUserInfo.user_name)}}</div>
                            <div class="bbbug_search_song_tips">你正在为Ta选一首歌送出</div>
                            <div class="bbbug_search_song_cancel" @click="clearUser">取消</div>
                        </div>
                    </div>
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
                    page: 1,
                    list: [],
                    userInfo: false,
                    roomInfo: false,
                    atSongUserInfo: false,
                }
            },
            created() {
                this.userInfo = this.global.userInfo;
                this.roomInfo = Object.assign({}, this.global.roomInfo);
                this.atSongUserInfo = this.global.atSongUserInfo;
                this.getList();
            },
            methods: {
                /**
                 * @description: 滚动事件
                 * @param {event} 事件
                 * @return {null}
                 */
                onScroll(e) {
                    let that = this;
                    if (e.target.scrollHeight - e.target.scrollTop < e.target.clientHeight + 50 && !that.bbbug_loading) {
                        that.page++;
                        that.getList();
                    }
                },
                /**
                 * @description: 取消送歌
                 * @param {null}
                 * @return {null}
                 */
                clearUser() {
                    let that = this;
                    that.atSongUserInfo = false;
                    that.global.atSongUserInfo = false;
                },
                /**
                 * @description: 通过歌手名称搜索歌曲
                 * @param {歌曲信息}
                 * @return {null}
                 */
                searchSongBySinger(item) {
                    this.global.songKeyword = item.singer;
                    this.$parent.showSearchSongs();
                },
                /**
                 * @description: 获取数据
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
                        url: "song/mySongList",
                        data: {
                            order: 'recent',
                            page: that.page
                        },
                        success(res) {
                            that.bbbug_loading = false;
                            if (that.page == 1) {
                                that.list = res.data;
                            } else {
                                for (let i = 0; i < res.data.length; i++) {
                                    that.list.push(res.data[i]);
                                }
                            }
                        },
                        error(res) {
                            that.bbbug_loading = false;
                        }
                    });
                },
                /**
                 * @description: 点歌
                 * @param {int} 点歌索引
                 * @return {null}
                 */
                addSong(index) {
                    let that = this;
                    if (that.list[index].loading) {
                        return;
                    }
                    that.list[index].loading = true;
                    that.$forceUpdate();
                    that.request({
                        url: "song/addSong",
                        data: {
                            room_id: that.global.room_id,
                            mid: that.list[index].mid,
                            at: that.atSongUserInfo ? that.atSongUserInfo.user_id : null
                        },
                        success(res) {
                            that.$message.success(res.msg);
                            that.list[index].loading = false;
                            that.clearUser();
                            that.$forceUpdate();
                        },
                        error(res) {
                            that.list[index].loading = false;
                            that.$forceUpdate();
                        }
                    });
                },
                /**
                 * @description: 播放歌曲
                 * @param {int} 歌曲索引
                 * @return {null}
                 */
                playSong(index) {
                    let that = this;
                    if (that.list[index].loading) {
                        return;
                    }
                    that.list[index].loading = true;
                    that.$forceUpdate();
                    that.request({
                        url: "song/playSong",
                        data: {
                            room_id: that.global.room_id,
                            mid: that.list[index].mid
                        },
                        success(res) {
                            that.$message.success(res.msg);
                            that.list[index].loading = false;
                            that.$forceUpdate();
                        },
                        error(res) {
                            that.list[index].loading = false;
                            that.$forceUpdate();
                        }
                    });
                },
                /**
                 * @description: 移除收藏
                 * @param {int} 歌曲索引
                 * @return {null}
                 */
                removeSong(index) {
                    let that = this;
                    if (that.list[index].loading) {
                        return;
                    }
                    that.list[index].loading = true;
                    that.$forceUpdate();
                    that.request({
                        url: "song/deleteMySong",
                        data: {
                            room_id: that.global.room_id,
                            mid: that.list[index].mid
                        },
                        success(res) {
                            that.$message.success(res.msg);
                            that.list[index].loading = false;
                            that.list.splice(index,1);
                            that.$forceUpdate();
                        },
                        error(res) {
                            that.list[index].loading = false;
                            that.$forceUpdate();
                        }
                    });
                }
            },
        }
</script>

<style>
    .bbbug_singer_hover:hover {
        color: orangered;
    }
</style>