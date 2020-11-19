<template>
    <div id="RoomCreate">
        <div class="bbbug_main_right">
            <div class="bbbug_main_right_room">
                <div class="bbbug_main_right_room_title">创建房间
                    <div class="bbbug_main_right_song_right" @click="createRoom">创建</div>
                </div>
                <el-form label-width="90px" class="bbbug_room_setting_form">
                    <el-form-item label="房间名称">
                        <el-input v-model="roomInfo.room_name" placeholder="请输入房间名称"></el-input>
                    </el-form-item>
                    <el-form-item label="房间公告">
                        <el-input v-model="roomInfo.room_notice" placeholder="请输入房间公告"></el-input>
                    </el-form-item>
                    <el-form-item label="二级域名">
                        <el-input v-model="roomInfo.room_domain" placeholder="请输入一个二级域名前缀"></el-input>
                    </el-form-item>
                    <div v-if="roomInfo.room_domain" class="bbbug_room_domain_tips">
                        地址：<a target="_blank" style="text-decoration:none;color:#666;"
                            :href="'https://'+roomInfo.room_domain+'.bbbug.com'">
                            {{roomInfo.room_domain}}.bbbug.com</a>
                    </div>
                    <el-form-item label="房间类型">
                        <el-select size="small" v-model="roomInfo.room_type" placeholder="请选择房间类型">
                            <el-option v-for="(item,index) in room_type" :label="item.title" :value="item.value">
                            </el-option>
                        </el-select>
                    </el-form-item>
                </el-form>
            </div>
        </div>
    </div>
</template>
<script>
    export
        default {
            data() {
                return {
                    userInfo: null,
                    roomInfo: {
                        room_type: 1,
                    },
                    room_type: [{
                        value: 0,
                        title: "文字聊天房"
                    }, {
                        value: 1,
                        title: "点歌音乐房"
                    }, {
                        value: 4,
                        title: "房主电台房"
                    }],
                }
            },
            created() {
                this.userInfo = this.global.userInfo;
            },
            methods: {
                createRoom() {
                    let that = this;
                    that.request({
                        url: "room/create",
                        data: Object.assign({}, that.roomInfo),
                        success(res) {
                            that.$confirm('房间创建成功，是否进入你的房间?', '创建成功', {
                                confirmButtonText: '进入',
                                cancelButtonText: '取消',
                                type: 'warning'
                            }).then(function () {
                                localStorage.setItem('room_change_id', res.data.room_id);
                                that.$parent.hideAll();
                                that.$parent.getUserInfo();
                            }).catch(function () {
                                that.$parent.hideAll();
                            });
                        }
                    });
                },
            },
        }
</script>
<style>
    .bbbug_room_setting_form {
        background-color: white;
        padding: 20px;
        padding-left: 0;
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        top: 55px;
        overflow: hidden;
        overflow-y: auto;
    }


    .el-select {
        display: block;
    }


    .bbbug_room_domain_tips {
        font-size: 12px;
        color: #999;
        margin-bottom: 20px;
        text-align: left;
        padding: 0px 30px;
    }
</style>