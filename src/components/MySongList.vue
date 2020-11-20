<template>
    <div id="MySongList">
        <div class="bbbug_main_right">
            <div class="bbbug_main_right_song">
                <div class="bbbug_main_right_song_title">我点过的歌</div>
                <div class="bbbug_main_right_song_list" v-loading="bbbug_loading">
                    <div class="bbbug_scroll" v-if="list.length>0" @scroll="onScroll">
                        <div class="bbbug_main_right_song_item" v-for="(item,index) in list" v-loading="item.loading">
                            <div class="bbbug_main_right_song_name">{{item.name}}
                            </div>
                            <div class="bbbug_main_right_song_singer">
                                <span class="bbbug_main_right_song_played">({{item.played}})</span>
                                歌手:{{item.singer}}
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
                }
            },
            created() {
                this.userInfo = this.global.userInfo;
                this.roomInfo = Object.assign({}, this.global.roomInfo);
                this.getList();
            },
            methods: {
                onScroll(e) {
                    let that = this;
                    if (e.target.scrollHeight - e.target.scrollTop < e.target.clientHeight + 50 && !that.bbbug_loading) {
                        that.page++;
                        that.getList();
                    }
                },
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
                            that.$forceUpdate();
                            that.page = 1;
                            that.getList();
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

</style>