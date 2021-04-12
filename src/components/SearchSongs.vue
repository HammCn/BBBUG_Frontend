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
                        <div class="bbbug_main_right_song_item_title" v-if="isHots">本周点歌热门歌曲推荐</div>
                        <div class="bbbug_main_right_song_item" v-for="(item, index) in list" v-loading="item.loading">
                            <div class="bbug_main_right_song_pic">
                                <img :data="getStaticUrl(item.pic)" :src="getStaticUrl('new/images/loading.gif')"
                                    onload="this.src=this.attributes['data'].value;this.attributes['onload']=null;"
                                    :onerror="getStaticUrl('new/images/nohead.jpg')" />
                            </div>
                            <div class="bbbug_main_right_song_name">
                                <font color=orangered v-if="isHots && item.week">({{item.week}})</font>
                                <span class="bbbug_singer_hover" @click="searchSongByKeyword(item.name)"
                                    :title="'搜索 '+item.name+' 的歌曲'">{{item.name}}</span>
                            </div>
                            <div class="bbbug_main_right_song_singer">
                                <span class="bbbug_singer_hover" @click="searchSongByKeyword(item.singer)"
                                    :title="'搜索 '+item.singer+' 的歌曲'">{{item.singer}}</span> <span
                                    v-if="item.album">({{item.album}})</span>
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
                isHots: true,
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
            } else {
                this.getHots();
            }
            let historyList = localStorage.getItem('search_history') || false;
            if (historyList) {
                this.historyList = JSON.parse(historyList);
            }
        },
        methods: {
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
             * @description: 关键词搜索歌曲
             * @param {string} 关键词 
             * @return {null}
             */
            searchSongByKeyword(keyword) {
                this.keyword = keyword;
                this.getList();
            },
            /**
             * @description: 获取热门歌曲
             * @param {null} 
             * @return {null}
             */
            getHots() {
                let that = this;
                that.isHots = true;
                that.bbbug_loading = true;
                that.request({
                    url: "song/search",
                    data: {
                        room_id: that.global.room_id,
                        isHots: that.isHots ? 1 : 0
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
            /**
             * @description: 获取搜索结果
             * @param {null} 
             * @return {null}
             */
            getList() {
                let that = this;
                that.isHots = false;
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
                        isHots: that.isHots ? 1 : 0
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
            /**
             * @description: 搜索下拉回调
             * @param {string} 搜索词 
             * @param {function} 下拉回调方法
             * @return {null}
             */
            querySearch(queryString, cb) {
                //设置历史
                if (this.keyword) {
                    cb([]);
                } else {
                    cb(JSON.parse(JSON.stringify(this.historyList)));
                }
            },
            /**
             * @description: 下拉回调
             * @param {obj} 选择的数据 
             * @return {null}
             */
            handleSelect(item) {
                this.keyword = item.value;
                this.getList();
            },
            /**
             * @description: 点歌
             * @param {int} 歌曲索引
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
            }
        },
    };
</script>
<style>
    .bbbug_singer_hover:hover {
        color: orangered;
    }

    .bbbug_main_right_song_item_title {
        text-align: left;
        padding: 0px 10px 5px 10px;
        color: #aaa;
        font-size: 12px;
    }
</style>