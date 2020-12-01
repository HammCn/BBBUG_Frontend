<template>
    <div id="SearchSongs">
        <div class="bbbug_main_right">
            <div class="bbbug_main_right_song">
                <div class="bbbug_main_right_song_title">
                    歌曲搜索
                    <el-autocomplete @select="handleSelect" :fetch-suggestions="querySearch"
                        style="margin-top: 20px;display:block;" placeholder="歌名/歌手/专辑搜索" size="small" v-model="keyword"
                        clearable @keydown.13.native="getList">
                        <el-button slot="append" icon="el-icon-search" @click="getList">搜索</el-button>
                    </el-autocomplete>
                </div>
                <div class="bbbug_main_right_song_list_search" v-loading="bbbug_loading">
                    <div class="bbbug_scroll">
                        <div class="bbbug_main_right_song_item" v-for="(item, index) in list" v-loading="item.loading">
                            <div class="bbbug_main_right_song_name">
                                {{item.name}}
                            </div>
                            <div class="bbbug_main_right_song_singer">
                                歌手: <span class="bbbug_singer_hover" @click="searchSongBySinger(item)"
                                    :title="'搜索 '+item.singer+' 的歌曲'">{{item.singer}}</span>
                            </div>
                            <div class="bbbug_main_right_song_controll">
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
                    <div class="bbbug_tips" v-if="list.length==0">输入精准词可快速搜索</div>

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
    export default {
        data() {
            return {
                bbbug_loading: false,
                list: [],
                keyword: "",
                userInfo: false,
                roomInfo: false,
                historyList: [],
                atSongUserInfo: false,
            };
        },
        created() {
            this.userInfo = this.global.userInfo;
            this.roomInfo = Object.assign({}, this.global.roomInfo);
            this.atSongUserInfo = this.global.atSongUserInfo;
            if (this.global.songKeyword) {
                this.keyword = this.global.songKeyword;
                this.getList();
            }
            let historyList = localStorage.getItem('search_history') || false;
            if (historyList) {
                this.historyList = JSON.parse(historyList);
            }
        },
        methods: {
            clearUser() {
                let that = this;
                that.atSongUserInfo = false;
                that.global.atSongUserInfo = false;
            },
            searchSongBySinger(item) {
                this.keyword = item.singer;
                this.getList();
            },
            getList() {
                let that = this;
                that.bbbug_loading = true;
                if (that.keyword) {
                    if (that.historyList.length > 10) {
                        that.historyList.pop();
                    }
                    for (let i = 0; i < that.historyList.length; i++) {
                        if (that.historyList[i].value == that.keyword) {
                            that.historyList.splice(i, 1);
                        }
                    }
                    that.historyList.unshift({
                        value: that.keyword
                    });
                    localStorage.setItem("search_history", JSON.stringify(that.historyList));
                }
                that.request({
                    url: "song/search",
                    data: {
                        room_id: that.global.room_id,
                        keyword: that.keyword,
                    },
                    success(res) {
                        that.bbbug_loading = false;
                        that.list = res.data;
                    },
                    error(res) {
                        that.bbbug_loading = false;
                    }
                });
            },
            querySearch(queryString, cb) {
                //设置历史
                cb(JSON.parse(JSON.stringify(this.historyList)));
                console.log(this.historyList);
            },
            handleSelect(item) {
                this.keyword = item.value;
                this.getList();
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
            }
        },
    };
</script>
<style>
    .bbbug_singer_hover:hover {
        color: orangered;
    }
</style>