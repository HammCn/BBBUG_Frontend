<template>
    <div id="RoomSetting">
        <div class="bbbug_main_right">
            <div class="bbbug_main_right_room">
                <div class="bbbug_main_right_room_title">房间设置
                    <div class="bbbug_main_right_song_right" @click="updateRoom">保存</div>
                </div>
                <el-form label-width="100px" class="bbbug_room_setting_form">
                    <el-form-item label="房间名称">
                        <el-input v-model="roomInfo.room_name" placeholder="请输入房间名称"></el-input>
                    </el-form-item>
                    <el-form-item label="房间公告">
                        <el-input v-model="roomInfo.room_notice" placeholder="请输入房间公告"></el-input>
                    </el-form-item>
                    <el-form-item label="隐藏房间">
                        <el-select size="small" v-model="roomInfo.room_hide" placeholder="是否隐藏房间">
                            <el-option v-for="(item,index) in room_hide" :label="item.title" :value="item.value">
                            </el-option>
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="房间权限">
                        <el-select size="small" v-model="roomInfo.room_public" placeholder="请选择房间权限类别">
                            <el-option v-for="(item,index) in room_public" :label="item.title" :value="item.value">
                            </el-option>
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="房间密码" v-if="roomInfo.room_public==1">
                        <el-input size="small" autocomplete="off" placeholder="" v-model="roomInfo.room_password">
                        </el-input>
                    </el-form-item>
                    <el-form-item label="全员禁言">
                        <el-select size="small" v-model="roomInfo.room_sendmsg" placeholder="请选择是否全员禁言">
                            <el-option v-for="(item,index) in room_sendmsg" :label="item.title" :value="item.value">
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="房间类型">
                        <el-select size="small" v-model="roomInfo.room_type" placeholder="请选择房间类型">
                            <el-option v-for="(item,index) in room_type" :label="item.title" :value="item.value">
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="机器点歌" v-if="roomInfo.room_type==1">
                        <el-select size="small" v-model="roomInfo.room_robot" placeholder="请选择机器人是否点歌">
                            <el-option v-for="(item,index) in room_robot" :label="item.title" :value="item.value">
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="点歌间隔" v-if="roomInfo.room_type==1 || roomInfo.room_type==4">
                        <el-input size="small" autocomplete="off" placeholder="" v-model="roomInfo.room_addsongcd">
                        </el-input>
                    </el-form-item>
                    <el-form-item label="顶歌间隔" v-if="roomInfo.room_type==1 || roomInfo.room_type==4">
                        <el-input size="small" autocomplete="off" placeholder="" v-model="roomInfo.room_pushsongcd">
                        </el-input>
                    </el-form-item>
                    <el-form-item label="日顶次数" v-if="roomInfo.room_type==1 || roomInfo.room_type==4">
                        <el-input size="small" autocomplete="off" placeholder="" v-model="roomInfo.room_pushdaycount">
                        </el-input>
                    </el-form-item>
                    <el-form-item label="点歌数量" v-if="roomInfo.room_type==1 || roomInfo.room_type==4">
                        <el-input size="small" autocomplete="off" placeholder="" v-model="roomInfo.room_addcount">
                        </el-input>
                    </el-form-item>
                    <el-form-item label="投票切歌" v-if="roomInfo.room_type==1">
                        <el-select size="small" v-model="roomInfo.room_votepass" placeholder="请选择是否开启投票切歌">
                            <el-option v-for="(item,index) in room_votepass" :label="item.title" :value="item.value">
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="投票比例" v-if="roomInfo.room_type==1 && roomInfo.room_votepass==1">
                        <el-select size="small" v-model="roomInfo.room_votepercent" placeholder="请选择投票比例">
                            <el-option v-for="(item,index) in room_votepercent" :label="item.title" :value="item.value">
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="开启点歌" v-if="roomInfo.room_type==1 || roomInfo.room_type==4">
                        <el-select size="small" v-model="roomInfo.room_addsong" placeholder="请选择是否开启点歌">
                            <el-option v-for="(item,index) in room_addsong" :label="item.title" :value="item.value">
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="循环方式" v-if="roomInfo.room_type==4">
                        <el-select size="small" v-model="roomInfo.room_playone" placeholder="请选择歌曲播放循环方式">
                            <el-option v-for="(item,index) in room_playone" :label="item.title" :value="item.value">
                            </el-option>
                        </el-select>
                    </el-form-item>
                </el-form>
                <div class="bbbug_room_setting_clear">
                    <button class="bbbug_room_setting_clear_button" @click="clearHistory">清理聊天记录</button>
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
                    userInfo: null,
                    roomInfo: {},
                    room_addsong: [{
                        value: 0,
                        title: "所有人可点歌"
                    }, {
                        value: 1,
                        title: "仅房主可点歌"
                    }],
                    room_sendmsg: [{
                        value: 0,
                        title: "关闭全员禁言"
                    }, {
                        value: 1,
                        title: "开启全员禁言"
                    }],
                    room_hide: [{
                        value: 0,
                        title: "显示到列表公共房间"
                    }, {
                        value: 1,
                        title: "从列表隐藏并独立房间"
                    }],
                    room_public: [{
                        value: 0,
                        title: "公开房间"
                    }, {
                        value: 1,
                        title: "加密房间"
                    }],
                    room_robot: [{
                        value: 0,
                        title: "开启机器人点歌"
                    }, {
                        value: 1,
                        title: "关闭机器人点歌"
                    }],
                    room_playone: [{
                        value: 0,
                        title: "随机播放"
                    }, {
                        value: 1,
                        title: "单曲循环"
                    }],
                    room_votepass: [{
                        value: 0,
                        title: "关闭投票切歌"
                    }, {
                        value: 1,
                        title: "打开投票切歌"
                    }],
                    room_votepercent: [{
                        value: 20,
                        title: "20%"
                    }, {
                        value: 30,
                        title: "30%"
                    }, {
                        value: 40,
                        title: "40%"
                    }, {
                        value: 50,
                        title: "50%"
                    }, {
                        value: 60,
                        title: "60%"
                    },],
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
                this.roomInfo = Object.assign({}, this.global.roomInfo);
            },
            methods: {
                updateRoom() {
                    let that = this;
                    that.request({
                        url: "room/saveMyRoom",
                        data: Object.assign({}, that.roomInfo, {
                            room_id: that.global.room_id
                        }),
                        success(res) {
                            that.$message.success(res.msg);
                        }
                    });
                },
                clearHistory() {
                    var that = this;
                    that.$confirm('是否确认清空当前房间聊天记录?', '删除聊天记录', {
                        confirmButtonText: '删除',
                        cancelButtonText: '取消',
                        type: 'warning'
                    }).then(function () {
                        that.request({
                            url: "message/clear",
                            data: {
                                room_id: that.global.room_id,
                            },
                            success(res) {
                                that.$message.success('删除房间聊天记录成功');
                            }
                        });
                    }).catch(function () { });
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
        bottom: 60px;
        top: 55px;
        overflow: hidden;
        overflow-y: auto;
    }


    .el-select {
        display: block;
    }

    .bbbug_room_setting_clear {
        position: absolute;
        left: 0;
        right: 10px;
        bottom: 10px;
        text-align: right;
    }

    .bbbug_room_setting_clear_button {
        background-color: orangered;
        color: white;
        padding: 10px 30px;
        border-radius: 5px;
        border: none;
        outline: none;
        cursor: pointer;
    }

    .bbbug_room_setting_clear_button:active {
        background-color: red;
    }

    .bbbug_room_domain_tips {
        font-size: 12px;
        color: #999;
        margin-bottom: 20px;
        text-align: left;
        padding: 0px 30px;
    }
</style>