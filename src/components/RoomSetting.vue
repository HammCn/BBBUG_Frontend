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
                            <el-option v-for="item in room_hide" :key="item.value" :label="item.title" :value="item.value">
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="房间权限">
                        <el-select size="small" v-model="roomInfo.room_public" placeholder="请选择房间权限类别">
                            <el-option v-for="item in room_public" :key="item.value" :label="item.title" :value="item.value">
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="房间密码" v-if="roomInfo.room_public==1">
                        <el-input size="small" autocomplete="off" placeholder="请输入房间密码,不修改请留空"
                            v-model="roomInfo.room_password">
                        </el-input>
                    </el-form-item>
                    <el-form-item label="房间插件">
                        <el-input v-model="roomInfo.room_app" placeholder="请输入插件https地址"></el-input>
                    </el-form-item>
                    <el-form-item label="房间背景">
                        <el-input v-model="roomInfo.room_background" placeholder="请上传房间背景图" readonly="readonly">
                            <template></template>
                            <el-upload slot="append" :action="uploadUrl" :show-file-list="false"
                                :on-success="handleBackgroudUploadSuccess" :before-upload="doUploadBefore"
                                :data="baseData">选择
                            </el-upload>
                        </el-input>
                    </el-form-item>
                    <el-form-item label="全员禁言">
                        <el-select size="small" v-model="roomInfo.room_sendmsg" placeholder="请选择是否全员禁言">
                            <el-option v-for="item in room_sendmsg" :key="item.value" :label="item.title" :value="item.value">
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="房间类型">
                        <el-select size="small" v-model="roomInfo.room_type" placeholder="请选择房间类型">
                            <el-option v-for="item in room_type" :key="item.value" :label="item.title" :value="item.value">
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="机器点歌" v-if="roomInfo.room_type==1">
                        <el-select size="small" v-model="roomInfo.room_robot" placeholder="请选择机器人是否点歌">
                            <el-option v-for="item in room_robot"  :key="item.value" :label="item.title" :value="item.value">
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="点歌间隔" v-if="roomInfo.room_type==1 || roomInfo.room_type==4">
                        <el-input size="small" autocomplete="off" placeholder="点歌时间间隔(单位秒)"
                            v-model="roomInfo.room_addsongcd">
                        </el-input>
                    </el-form-item>
                    <el-form-item label="顶歌间隔" v-if="roomInfo.room_type==1 || roomInfo.room_type==4">
                        <el-input size="small" autocomplete="off" placeholder="顶歌时间间隔(单位秒)"
                            v-model="roomInfo.room_pushsongcd">
                        </el-input>
                    </el-form-item>
                    <el-form-item label="日顶次数" v-if="roomInfo.room_type==1 || roomInfo.room_type==4">
                        <el-input size="small" autocomplete="off" placeholder="每天允许顶歌次数"
                            v-model="roomInfo.room_pushdaycount">
                        </el-input>
                    </el-form-item>
                    <el-form-item label="点歌数量" v-if="roomInfo.room_type==1 || roomInfo.room_type==4">
                        <el-input size="small" autocomplete="off" placeholder="播放列表允许点歌数量"
                            v-model="roomInfo.room_addcount">
                        </el-input>
                    </el-form-item>
                    <el-form-item label="投票切歌" v-if="roomInfo.room_type==1">
                        <el-select size="small" v-model="roomInfo.room_votepass" placeholder="请选择是否开启投票切歌">
                            <el-option v-for="item in room_votepass" :key="item.value" :label="item.title" :value="item.value">
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="投票比例" v-if="roomInfo.room_type==1 && roomInfo.room_votepass==1">
                        <el-select size="small" v-model="roomInfo.room_votepercent" placeholder="请选择投票比例">
                            <el-option v-for="item in room_votepercent" :key="item.value" :label="item.title" :value="item.value">
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="开启点歌" v-if="roomInfo.room_type==1 || roomInfo.room_type==4">
                        <el-select size="small" v-model="roomInfo.room_addsong" placeholder="请选择是否开启点歌">
                            <el-option v-for="item in room_addsong" :key="item.value" :label="item.title" :value="item.value">
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="循环方式" v-if="roomInfo.room_type==4">
                        <el-select size="small" v-model="roomInfo.room_playone" placeholder="请选择歌曲播放循环方式">
                            <el-option v-for="item in room_playone" :key="item.value" :label="item.title" :value="item.value">
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
                    uploadUrl: "",
                    baseData: false,
                    room_addsong: [{
                        value: 0,
                        title: "所有人可点歌"
                    }, {
                        value: 1,
                        title: "仅房主可点歌"
                    }],
                    room_sendmsg: [{
                        value: 0,
                        title: "所有人可发言"
                    }, {
                        value: 1,
                        title: "管理房主可发言"
                    }, {
                        value: 2,
                        title: "管理房主嘉宾可发言"
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
                this.uploadUrl = this.global.apiUrl + "/api/attach/uploadImage";
                this.baseData = this.global.baseData;
            },
            methods: {
                /**
                 * @description: 上传结果回调
                 * @param {obj} 服务器返回数据
                 * @param {file} 上传的本地文件 
                 * @return {null}
                 */
                handleBackgroudUploadSuccess(res, file) {
                    var that = this;
                    if (res.code == 200) {
                        that.roomInfo.room_background = that.getStaticUrl(res.data.attach_path);
                    } else {
                        that.$message.error(res.msg);
                    }
                },
                /**
                 * @description: 上传前验证
                 * @param {file} 验证的文件 
                 * @return {bool} 是否验证通过
                 */
                doUploadBefore(file) {
                    const isJPG = file.type === 'image/jpeg' || file.type === 'image/png';
                    const isLt2M = file.size / 1024 / 1024 < 2;

                    if (!isJPG) {
                        this.$message.error('发送图片只能是 JPG/PNG 格式!');
                    }
                    if (!isLt2M) {
                        this.$message.error('发送图片大小不能超过 2MB!');
                    }
                    return isJPG && isLt2M;
                },
                /**
                 * @description: 修改房间信息
                 * @param {null} 
                 * @return {null}
                 */
                updateRoom() {
                    let that = this;
                    that.request({
                        url: "room/saveMyRoom",
                        data: Object.assign({}, that.roomInfo, {
                            room_id: that.global.room_id
                        }),
                        success(res) {
                            that.$message.success(res.msg);
                            that.roomInfo.room_password = '';
                        }
                    });
                },
                /**
                 * @description: 清理房间聊天记录
                 * @param {null} 
                 * @return {null}
                 */
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