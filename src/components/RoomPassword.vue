<!--
 * @Author: your name
 * @Date: 2021-04-12 22:44:24
 * @LastEditTime: 2021-04-12 22:44:25
 * @LastEditors: your name
 * @Description: In User Settings Edit
 * @FilePath: /BBBUG_VUE/src/components/RoomPassword.vue
-->
<template>
    <div id="RoomPassword">
        <div class="bbbug_main_right">
            <div class="bbbug_main_right_room">
                <div class="bbbug_main_right_room_title">输入房间密码
                    <div class="bbbug_main_right_song_right" @click="enterRoom">进入</div>
                </div>
                <el-form label-width="80px" class="bbbug_room_setting_form">
                    <el-form-item label="房间密码">
                        <el-input v-model="room_password" placeholder="请输入房间密码" @keydown.13.native="enterRoom">
                        </el-input>
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
                    room_password: ""
                }
            },
            created() {
                if (!this.global.userInfo) {
                    localStorage.setItem('room_change_id', 888);
                    this.$parent.hideAll();
                    this.$parent.getRoomInfo();
                    return;
                }
                this.userInfo = this.global.userInfo;
            },
            methods: {
                /**
                 * @description: 进入房间
                 * @param {null} 
                 * @return {null}
                 */
                enterRoom() {
                    this.global.room_password = this.room_password;
                    this.$parent.hideAll();
                    this.$parent.getRoomInfo();
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