<template>
    <div id="RoomSetting">
        <div class="bbbug_main_right">
            <div class="bbbug_main_right_room">
                <div class="bbbug_main_right_room_title">我的设置
                    <div class="bbbug_main_right_song_right" @click="updateMyInfo">保存</div>
                </div>
                <el-form label-width="60px" class="bbbug_my_setting__form">
                    <div style="text-align: center;margin-bottom: 20px;">
                        <el-upload :action="uploadHeadUrl" :show-file-list="false"
                            :on-success="handleProfileHeadUploadSuccess" :before-upload="doUploadBefore"
                            :data="baseData">
                            <img :src="userInfo.user_head" onerror="this.src='//cdn.bbbug.com/images/nohead.jpg'"
                                style="border-radius: 100%;width:80px;height:80px;" />
                        </el-upload>
                        <div>ID:
                            <font color=orangered style="margin-left:5px;font-weight: bolder;">
                                {{userInfo.user_id}}</font>
                        </div>
                    </div>
                    <el-form-item label="昵称">
                        <el-input size="medium" autocomplete="off" placeholder="请输入你的昵称" v-model="userInfo.user_name">
                        </el-input>
                    </el-form-item>
                    <el-form-item label="签名">
                        <el-input size="medium" autocomplete="off" placeholder="请输入你的签名" v-model="userInfo.user_remark">
                        </el-input>
                    </el-form-item>
                    <el-form-item label="摸摸">
                        <el-input size="medium" autocomplete="off" placeholder="" v-model="userInfo.user_touchtip">
                        </el-input>
                    </el-form-item>
                    <el-form-item label="性别">
                        <el-select size="medium" v-model="userInfo.user_sex" placeholder="请选择你的性别" class="allLine"
                            style="margin-left:0px;">
                            <el-option v-for="(item,index) in sexList" :label="item.title" :value="item.value">
                            </el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item label="密码">
                        <el-input size="medium" autocomplete="off" placeholder="你的密码,不修改请留空"
                            v-model="userInfo.user_password"></el-input>
                    </el-form-item>
                </el-form>
                <div class="bbbug_my_setting__clear">
                    <button class="bbbug_my_setting__clear_button" @click="logout">退出登录</button>
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
                    roomInfo: {},
                    uploadHeadUrl: "",
                    baseData: false,
                    sexList: [{
                        value: 0,
                        title: '女生',
                    }, {
                        value: 1,
                        title: '男生',
                    }],
                }
            },
            created() {
                if (!this.global.userInfo || !this.global.roomInfo) {
                    this.$router.push('/');
                    return;
                }
                this.userInfo = Object.assign({}, this.global.userInfo);
                this.roomInfo = Object.assign({}, this.global.roomInfo);

                this.userInfo.user_name = decodeURIComponent(this.userInfo.user_name);

                this.uploadHeadUrl = this.global.api.url + "attach/uploadHead";
                this.baseData = this.global.baseData;
                if (this.userInfo.user_id < 0) {
                    this.$emit('App', 'hideAll');
                    this.$router.push('/login');
                }
            },
            methods: {
                doUploadBefore(file) {
                    const isJPG = file.type === 'image/jpeg' || file.type === 'image/png' || file.type === 'image/gif';
                    const isLt2M = file.size / 1024 / 1024 < 2;

                    if (!isJPG) {
                        this.$message.error('发送图片只能是 JPG/PNG/GIF 格式!');
                    }
                    if (!isLt2M) {
                        this.$message.error('发送图片大小不能超过 2MB!');
                    }
                    return isJPG && isLt2M;
                },
                getImageUrl(url) {
                    if (!url) {
                        return '';
                    }
                    if (url.indexOf('https://') > -1 || url.indexOf('http://') > -1) {
                        return url.replace('http:', 'https:');
                    } else {
                        return 'https://cdn.bbbug.com/uploads/' + url;
                    }
                },
                handleProfileHeadUploadSuccess(res, file) {
                    var that = this;
                    if (res.code == 200) {
                        that.userInfo.user_head = that.getImageUrl(res.data.attach_thumb);
                    } else {
                        that.$message.error(res.msg);
                    }
                },
                updateMyInfo() {
                    let that = this;
                    that.request({
                        url: "user/updateMyInfo",
                        data: that.userInfo,
                        success(res) {
                            that.$message.success(res.msg);
                            that.$emit('App', 'getUserInfo');
                            that.$router.push('/');
                        }
                    });
                },
                logout() {
                    var that = this;
                    that.$confirm('是否确认退出当前登录的账号?', '退出登录', {
                        confirmButtonText: '退出',
                        cancelButtonText: '取消',
                        type: 'warning'
                    }).then(function () {
                        localStorage.removeItem('access_token');
                        that.global.userInfo = that.global.guestUserInfo;
                        that.global.baseData.access_token = that.global.guestUserInfo.access_token;
                        that.$emit('App', 'getUserInfo');
                        that.$router.push('/');
                    }).catch(function () { });
                },
            },
        }
</script>
<style>
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

    .el-select {
        display: block;
    }

    .bbbug_my_setting__clear {
        position: absolute;
        left: 0;
        right: 10px;
        bottom: 10px;
        text-align: right;
    }

    .bbbug_my_setting__clear_button {
        background-color: orangered;
        color: white;
        padding: 10px 30px;
        border-radius: 5px;
        border: none;
        outline: none;
        cursor: pointer;
    }

    .bbbug_my_setting__clear_button:active {
        background-color: red;
    }
</style>