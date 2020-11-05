<template>
    <div id="QQCallback">
        <div class="QQLoading" v-loading.fullscreen="true"></div>
    </div>
</template>
<script>
    export
        default {
            data() {
                return {
                }
            },
            created() {
                let that = this;
                let code = location.search.replace('?code=', '').replace('&state=https%3A%2F%2Fbbbug.com','');
                console.log(code);
                that.request({
                    url: 'user/thirdlogin',
                    data: {
                        from: "qq",
                        code: code,
                    },
                    success(res) {
                        that.global.baseData.access_token = res.data.access_token;
                        localStorage.setItem('access_token', res.data.access_token);
                        that.$emit('App', 'getUserInfo');
                        that.$router.push('/');
                    },
                    error(res) {
                        setTimeout(function () {
                            that.$router.push('/login');
                        }, 3000);
                    }
                });
            },
            methods: {
            },
        }
</script>
<style>
    .QQLoading {
        position: absolute;
        left: 0 !important;
        right: 0 !important;
        top: 0 !important;
        bottom: 0 !important;
    }
</style>