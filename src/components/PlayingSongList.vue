<template>
    <div id="PlaySongList">
        <div class="bbbug_main_right">
            <div class="bbbug_main_right_song">
                <div class="bbbug_main_right_song_title">正在等待播放的歌
                    <div @click="searchSong" class="bbbug_main_right_song_right">我要点歌</div>
                </div>
                <div class="bbbug_main_right_song_list" v-loading="bbbug_loading">
                    <div class="bbbug_scroll" v-if="list.length>0">
                        <div class="bbbug_main_right_song_item" v-for="(item,index) in list" v-loading="item.loading">
                            <div class="bbug_main_right_song_pic">
                                <img :data="getStaticUrl(item.song.pic)" :src="getStaticUrl('new/images/loading.gif')" class="xiaomi"
                                    onload="this.src=this.attributes['data'].value;this.attributes['onload']=null;"
                                    :onerror="getStaticUrl('new/images/nohead.jpg')" />
                            </div>
                            <div class="bbbug_main_right_song_name"><font class="orangered" v-if="item.push_count && item.push_count>0">({{item.push_count}})</font>{{item.song.name}}
                            </div>
                            <div class="bbbug_main_right_song_singer" v-if="item.at">
                                <font v-if="item.at.user_id == userInfo.user_id" color="orangered">
                                    {{urldecode(item.user.user_name)}} 送给 {{urldecode(item.at.user_name)}} 的歌
                                </font>
                                <font v-if="item.at.user_id != userInfo.user_id">
                                    <font color="#666">{{urldecode(item.user.user_name)}}</font> 送给 <font color="#666">
                                        {{urldecode(item.at.user_name)}}</font> 的歌
                                </font>
                            </div>
                            <div class="bbbug_main_right_song_singer" v-if="!item.at">
                                点歌人:<font color="#666">{{urldecode(item.user.user_name)}}</font> 歌手:{{item.song.singer}}
                            </div>
                            <div class="bbbug_main_right_song_controll">
                                <button class="bbbug_main_right_song_button bbbug_main_right_song_delete"
                                    v-if="userInfo.user_admin || userInfo.user_id == roomInfo.room_user || userInfo.user_id == item.user.user_id"
                                    @click="removeSong(index)">移除</button>
                                <button
                                    v-if="roomInfo.room_type==1 || (roomInfo.room_type==4 && roomInfo.room_user!=userInfo.user_id)"
                                    class="bbbug_main_right_song_button bbbug_main_right_song_add"
                                    @click="pushSong(index)">顶歌</button>
                                <button v-if="roomInfo.room_type==4 && roomInfo.room_user==userInfo.user_id"
                                    class="bbbug_main_right_song_button bbbug_main_right_song_add"
                                    @click="playSong(index)">播放</button>
                            </div>
                        </div>
                    </div>
                    <div class="bbbug_tips" v-if="list.length==0">已经没有歌啦,快去点歌吧~</div>
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
                /**
                 * @description: 显示本窗体
                 * @param {null} 
                 * @return {null}
                 */
                searchSong() {
                    this.$parent.hideAll();
                    this.$parent.dialog.SearchSongs = true;
                },
                /**
                 * @description: 搜索歌曲
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
                        url: "song/songList",
                        data: {
                            room_id: that.global.room_id
                        },
                        success(res) {
                            that.bbbug_loading = false;
                            that.list = res.data;
                            that.$emit('listUpdate', res.data);
                        },
                        error(res) {
                            that.bbbug_loading = false;
                        }
                    });
                },
                /**
                 * @description: 移除播放列表
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
                        url: "song/remove",
                        data: {
                            room_id: that.global.room_id,
                            mid: that.list[index].song.mid
                        },
                        success(res) {
                            that.$message.success(res.msg);
                            that.list[index].loading = false;
                            that.$forceUpdate();
                            that.getList();
                        },
                        error(res) {
                            that.list[index].loading = false;
                            that.$forceUpdate();
                        }
                    });
                },
                /**
                 * @description: 指定歌曲
                 * @param {int} 歌曲索引
                 * @return {null}
                 */
                pushSong(index) {
                    let that = this;
                    if (that.list[index].loading) {
                        return;
                    }
                    that.list[index].loading = true;
                    that.$forceUpdate();
                    that.request({
                        url: "song/push",
                        data: {
                            room_id: that.global.room_id,
                            mid: that.list[index].song.mid
                        },
                        success(res) {
                            that.$message.success(res.msg);
                            that.list[index].loading = false;
                            that.$forceUpdate();
                            that.getList();
                        },
                        error(res) {
                            that.list[index].loading = false;
                            that.$forceUpdate();
                        }
                    });
                },
                /**
                 * @description: 播放指定歌曲
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
                            mid: that.list[index].song.mid
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
            }
        }
</script>
<style>

</style>