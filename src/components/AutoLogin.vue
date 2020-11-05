<template>
    <div id="AutoLogin">
        <div class="AutoLogin" v-loading.fullscreen="true" v-if="isLoading"></div>
    </div>
</template>
<script>
    export
        default {
            data() {
                return {
                    isLoading: true
                }
            },
            created() {
                let that = this;
                let access_token = that.getQueryString('access_token') || '';
                let room_id = that.getQueryString('room_id') || false;
                if (access_token) {
                    that.global.baseData.access_token = access_token;
                    localStorage.setItem('access_token', access_token);
                }
                if (room_id) {
                    that.global.room_id = room_id;
                } else {
                    room_id = location.pathname.replace("/", '').replace(".html", "");
                    that.global.room_id = room_id || 888;
                }
                that.$emit('App', 'getUserInfo');
                that.$router.push('/');
                that.isLoading = false;
            },
            methods: {
                getQueryString(name) {
                    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
                    var r = window.location.search.substr(1).match(reg);
                    if (r != null) return unescape(r[2]); return null;
                }
            },
        }
</script>
<style>
    .AutoLogin {
        position: absolute;
        left: 0 !important;
        right: 0 !important;
        top: 0 !important;
        bottom: 0 !important;
    }
</style>