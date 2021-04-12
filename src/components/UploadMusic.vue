<template>
    <div id="UploadMusic">
        <div class="bbbug_upload_music_bg"></div>
        <div class="bbbug_upload_music" v-loading="loading">
            <div class="bbbug_upload_music_title">请输入歌曲信息</div>
            <el-form label-width="90px">
                <el-form-item label="歌曲名称">
                    <el-input v-model="songInfo.song_name" placeholder="请输入歌曲名称"></el-input>
                </el-form-item>
                <el-form-item label="歌手名称">
                    <el-input v-model="songInfo.song_singer" placeholder="请输入歌手名称"></el-input>
                </el-form-item>
                <el-form-item style="text-align: right;">
                    <audio ref="audio" @canplay="canplay" :src="songInfo.song_url" v-if="songInfo && songInfo.song_url"
                        control1></audio>
                    <div style="font-size:12px;color:orangered;margin-right:10px;">请勿上传违规违法音频,否则你将有可能被封号！</div>
                    <div @click="doUploadMusic" class="bbbug_upload_music_button">添加歌曲</div>
                    <div @click="closeWindow" class="bbbug_upload_music_button bbbug_upload_music_cancel">取消</div>
                </el-form-item>
            </el-form>
        </div>
    </div>
</template>
<script>
    export
        default {
            data() {
                return {
                    userInfo: null,
                    songInfo: {
                        song_name: "",
                        song_singer: "",
                        song_url: "",
                        song_length: 0,
                        song_pic: "",
                        song_mid: 0
                    },
                    loading: true
                }
            },
            created() {
                this.userInfo = this.global.userInfo;
                this.songInfo.song_url = this.global.uploadMusicUrl;
                this.songInfo.song_mid = this.global.uploadMusicMid;
                this.songInfo.song_pic = this.userInfo.user_head;
            },
            methods: {
                /**
                 * @description: 关闭当前窗口
                 * @param {null} 
                 * @return {null}
                 */
                closeWindow() {
                    this.$parent.uploadSongForm = false;
                },
                /**
                 * @description: 播放验证
                 * @param {null} 
                 * @return {null}
                 */
                canplay() {
                    this.songInfo.song_length = parseInt(this.$refs.audio.duration);
                    this.loading = false;
                },
                /**
                 * @description: 添加到我的歌单
                 * @param {null} 
                 * @return {null}
                 */
                doUploadMusic() {
                    let that = this;
                    if (that.songInfo.song_length == 0 || !that.songInfo.song_url || !that.songInfo.song_mid) {
                        that.$message.error('歌曲初始化失败,请重新上传!');
                        that.$parent.uploadSongForm = false;
                        return;
                    }
                    that.songInfo.song_name = that.songInfo.song_name.replace(/ /g, '').replace(/ /g, '');
                    if (that.songInfo.song_name.length == 0) {
                        that.$message.error('歌曲名称就显得非常重要了!');
                        return;
                    }
                    that.songInfo.song_singer = that.songInfo.song_singer.replace(/ /g, '').replace(/ /g, '');
                    if (that.songInfo.song_singer.length == 0) {
                        that.$message.error('你有必要告诉大家这首歌是谁演唱的!');
                        return;
                    }
                    this.loading = true;
                    that.request({
                        url: "song/addNewSong",
                        data: Object.assign({}, that.songInfo),
                        success(res) {
                            that.$message.success("歌曲添加成功,去你的歌单看看吧");
                            that.$parent.uploadSongForm = false;
                            this.loading = false;
                        },
                        error() {
                            this.loading = false;
                        }
                    });
                },
            },
        }
</script>
<style>
    .bbbug_upload_music_bg {
        position: fixed;
        left: 0;
        right: 0;
        top: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.3);
        backdrop-filter: blur(20px);
    }

    .bbbug_upload_music_title {
        margin-bottom: 40px;
        margin-left: 20px;
        font-size: 20px;
        color: #333;
    }

    .bbbug_upload_music {
        background-color: rgba(255, 255, 255, 0.9);
        position: relative;
        padding: 20px 30px 5px 10px;
        border-radius: 20px;
        width: 400px;
        overflow: hidden;
    }

    .bbbug_upload_music_button {
        background-color: #666;
        color: white;
        padding: 0px 20px;
        border-radius: 5px;
        cursor: pointer;
        display: inline-block;
        line-height: 36px;
        margin: 0px 5px;
    }


    .bbbug_upload_music_button:active,
    .bbbug_upload_music_button:hover {
        background-color: #555;
    }

    .bbbug_upload_music_cancel {
        background-color: #ccc;
        z-index: 2001;
        position: relative;
    }

    .bbbug_upload_music_cancel:active,
    .bbbug_upload_music_cancel:hover {
        background-color: #ddd;
    }
</style>