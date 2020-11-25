<template>
    <div id="RoomSetting">
        <div class="bbbug_main_right" style="width:250px;min-width: auto;">
            <div class="bbbug_main_right_room">
                <div class="bbbug_main_right_room_title">系统设置
                </div>
                <el-form label-width="120px" class="bbbug_my_setting__form">
                    <el-form-item label="通知声音">
                        <el-switch v-model="isEnableNoticePlayer" @change="isEnableNoticePlayerChanged"></el-switch>
                    </el-form-item>
                    <el-form-item label="显示通知">
                        <el-switch v-model="isEnableNotification" @change="isEnableNotificationChanged"></el-switch>
                    </el-form-item>
                    <el-form-item label="暗黑模式">
                        <el-switch v-model="isDarkModelTemp" @change="isDarkModelChanged"></el-switch>
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
                    userInfo: {},
                    roomInfo: {},
                    isEnableNoticePlayer: true,
                    isEnableNotification: true,
                    isDarkModelTemp: false
                }
            },
            created() {
                this.userInfo = Object.assign({}, this.global.userInfo);
                this.roomInfo = Object.assign({}, this.global.roomInfo);
                this.isEnableNoticePlayer = localStorage.getItem('isEnableNoticePlayer') != 1 ? true : false;
                this.isEnableNotification = localStorage.getItem('isEnableNotification') != 1 ? true : false;
                this.isDarkModelTemp = this.$parent.isDarkModel;
            },
            methods: {
                isEnableNoticePlayerChanged() {
                    localStorage.setItem('isEnableNoticePlayer', this.isEnableNoticePlayer ? 0 : 1);
                    this.$parent.loadConfig();
                },
                isEnableNotificationChanged() {
                    localStorage.setItem('isEnableNotification', this.isEnableNotification ? 0 : 1);
                    this.$parent.loadConfig();
                },
                isDarkModelChanged() {
                    this.$parent.updateDarkModel(this.isDarkModelTemp);
                }
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
        bottom: 0;
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