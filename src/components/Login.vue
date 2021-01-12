<template>
    <div id="login">
        <div class="bbbug_bg" @click.stop="isLocked=!isLocked;"
            :style="{backgroundImage:'url('+getStaticUrl(background)+')'}">
        </div>
        <div class="bbbug_login">
            <el-form :model="form" ref="bbbug_login_form" label-width="60px" class="bbbug_login_form"
                v-loading="bbbug_loading">
                <div class="bbbug_login_form_title">请先登录后快乐的玩耍吧~
                    <el-link class="bbbug_login_form_title_guest" @click="loginGuest">游客</el-link>
                </div>
                <el-form-item prop="user_account" label="账号"
                    :rules="[{required: true, message: '账号必须填写才能登录啊...', trigger: 'blur' }]">
                    <el-input placeholder="支持ID/邮箱登录" v-model="form.user_account">
                        <el-button slot="append" icon="el-icon-message" title="发送验证码到邮箱" @click="sendMail">
                            发送
                        </el-button>
                    </el-input>
                </el-form-item>
                <el-form-item prop="user_password" label="密码"
                    :rules="[{ required: true, message: '不填写密码如何登录???', trigger: 'blur' }]">
                    <el-input type="password" placeholder="登录密码或验证码" v-model="form.user_password"
                        @keydown.13.native="doLogin('bbbug_login_form')">
                    </el-input>
                </el-form-item>
                <el-form-item class="bbbug_login_form_submit" style="margin-left:10px;"> <span style="float:left;">
                        <el-link
                            :href="'https://graph.qq.com/oauth2.0/authorize?client_id='+global.appIdList.qq+'&redirect_uri='+localhost+'qq&response_type=code&state='+localhost" title="QQ">
                            <i class="iconfont icon-QQ-circle-fill" style="font-size:24px;margin-right:-1px;"></i>
                        </el-link>
                        <el-link
                            :href="'https://gitee.com/oauth/authorize?client_id='+global.appIdList.gitee+'&redirect_uri='+localhost+'gitee&response_type=code'" title="Gitee 码云">
                            <i class="iconfont icon-gitee"></i>
                        </el-link>
                        <el-link
                            :href="'https://www.oschina.net/action/oauth2/authorize?client_id='+global.appIdList.oschina+'&redirect_uri='+localhost+'oschina&response_type=code'" title="开源中国">
                            <i class="iconfont icon-icon-oschina-circle"></i>
                        </el-link>
                        <el-link
                            :href="'https://oapi.dingtalk.com/connect/qrconnect?appid='+global.appIdList.ding+'&response_type=code&scope=snsapi_login&state=STATE&redirect_uri='+localhost+'ding'" title="钉钉">
                            <i class="iconfont icon-dingding"></i>
                        </el-link>
                        <el-link
                            href="javascript:;" title="Github">
                            <i class="iconfont icon-git"></i>
                        </el-link>
                    </span>

                    <el-button type="primary" @click="doLogin('bbbug_login_form')">立即登录</el-button>
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
                background: "new/images/bg_dark.jpg",
                bbbug_loading: false,
                localhost: "",
                form: {
                    user_account: "",
                    user_password: "",
                }
            }
        },
        created() {
            this.localhost = encodeURIComponent(location.href);
            this.callParentFunction('needLogin', 'please login first!');
        },
        methods: {
            loginGuest() {
                this.$parent.loginGuest();
            },
            doLogin(formName) {
                let that = this;
                that.$refs[formName].validate(function(valid) {
                    if (valid) {
                        that.bbbug_loading = true;
                        that.request({
                            url: "user/login",
                            data: that.form,
                            success(res) {
                                that.bbbug_loading = false;
                                that.global.baseData.access_token = res.data.access_token;
                                localStorage.setItem('access_token', res.data.access_token);
                                that.$parent.hideAll();
                                that.$parent.getUserInfo();
                            },
                            error(res) {
                                that.bbbug_loading = false;
                            }
                        });
                    }
                });
            },
            sendMail() {
                let that = this;
                that.request({
                    url: "sms/email",
                    loading: true,
                    data: {
                        email: that.form.user_account
                    },
                    success(res) {
                        that.$message.success(res.msg);
                    }
                });
            }
        }
    }
</script>
<style>
    .bbbug_login {
        display: flex;
        display: -webkit-flex;
        align-items: center;
        justify-content: center;
        position: fixed;
        left: 0;
        right: 0;
        top: 0;
        bottom: 0;
    }
    
    .bbbug_login_form {
        width: 400px;
        background-color: white;
        padding: 10px 20px;
        padding-right: 30px;
        padding-bottom: 30px;
        border-radius: 10px;
        position: relative;
        box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.5);
    }
    
    .bbbug_login_form_title_guest {
        position: absolute;
        right: 20px;
        top: 20px;
    }
    
    .bbbug_login_form_title {
        margin-bottom: 20px;
        padding: 10px 20px;
        font-size: 18px;
    }
    
    .bbbug_login_form_submit {
        text-align: right;
        margin-bottom: 0px !important;
    }
    
    .bbbug_login_form_submit .el-form-item__content {
        margin-left: 10px !important;
    }
    .bbbug_login_form_submit a{
        text-decoration: nonen;
    }
    .bbbug_login_form_submit .iconfont{
        font-size:20px;
    }
    
    .el-loading-mask {
        border-radius: 10px;
    }
</style>