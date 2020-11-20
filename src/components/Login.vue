<template>
    <div id="login">
        <div class="bbbug_bg"></div>
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
                            href="https://graph.qq.com/oauth2.0/authorize?client_id=101904044&redirect_uri=https%3A%2F%2Fbbbug.com%2Fqq&response_type=code&state=https%3A%2F%2Fbbbug.com">
                            QQ
                        </el-link>
                        <el-link
                            href="https://gitee.com/oauth/authorize?client_id=d2c3e3c6f5890837a69c65585cc14488e4075709db1e89d4cb4c64ef1712bdbb&redirect_uri=https%3A%2F%2Fbbbug.com%2Fgitee&response_type=code">
                            码云
                        </el-link>
                        <el-link
                            href="https://www.oschina.net/action/oauth2/authorize?client_id=utwQOfbgBgBcwBolfNft&redirect_uri=https%3A%2F%2Fbbbug.com%2Foschina&response_type=code">
                            开源中国
                        </el-link>
                        <el-link
                            href="https://oapi.dingtalk.com/connect/qrconnect?appid=dingoag8afgz20g2otw0jf&response_type=code&scope=snsapi_login&state=STATE&redirect_uri=https://bbbug.com/ding">
                            钉钉
                        </el-link>
                        <!-- <el-link href="http://github.com/login/oauth/authorize?client_id=Iv1.c287a5d998f38f1f&redirect_uri=https://bbbug.com/github">
                            Github
                        </el-link> -->
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
                    bbbug_loading: false,
                    form: {
                        user_account: "",
                        user_password: "",
                    }
                }
            },
            created() {
                this.callParentFunction('needLogin', 'please login first!');
                this.$emit("App", 'hideAllDialog');
            },
            methods: {
                loginGuest() {
                    this.$parent.loginGuest();
                },
                doLogin(formName) {
                    let that = this;
                    that.$refs[formName].validate(function (valid) {
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

    .el-loading-mask {
        border-radius: 10px;
    }
</style>