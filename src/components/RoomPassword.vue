<template>
    <div id="RoomPassword">
        <div class="bbbug_main_right">
            <div class="bbbug_main_right_room">
                <div class="bbbug_main_right_room_title">输入房间密码
                    <div class="bbbug_main_right_song_right" @click="enterRoom">进入</div>
                </div>
                <el-form label-width="80px" class="bbbug_room_setting_form">
                    <el-form-item label="房间密码">
                        <el-input v-model="room_password" placeholder="请输入房间密码"></el-input>
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
                    roomInfo: {},
                    room_password:""
                }
            },
            created() {
                if (!this.global.userInfo) {
                    this.$router.push('/');
                    return;
                }
                this.userInfo = this.global.userInfo;
            },
            methods: {
                enterRoom() {
                    let that = this;
                    that.global.room_password = that.room_password;
                    that.$emit('App', 'getRoomInfo');
                    that.$router.push('/');
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

</style>