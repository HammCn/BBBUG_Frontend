<template>
    <div id="RoomSetting">
        <div class="bbbug_main_right" style="width:250px;min-width: auto;">
            <div class="bbbug_main_right_room">
                <div class="bbbug_main_right_room_title">系统设置
                </div>
                <el-form label-width="120px" class="bbbug_my_setting__form">
                    <el-form-item label="被@提醒">
                        <el-switch v-model="isEnableAtNotification" @change="isEnableAtNotificationChanged"></el-switch>
                    </el-form-item>
                    <el-form-item label="摸摸提醒">
                        <el-switch v-model="isEnableTouchNotice" @change="isEnableTouchNoticeChanged"></el-switch>
                    </el-form-item>
                    <el-form-item label="声音提醒">
                        <el-switch v-model="isEnableNoticePlayer" @change="isEnableNoticePlayerChanged"></el-switch>
                    </el-form-item>
                    <el-form-item label="浏览器通知">
                        <el-switch v-model="isEnableNotification" @change="isEnableNotificationChanged"></el-switch>
                    </el-form-item>
                    <el-form-item label="暗黑模式">
                        <el-switch v-model="isDarkModelTemp" @change="isDarkModelChanged"></el-switch>
                    </el-form-item>
                    <el-form-item label="开启频谱">
                      <el-switch v-model="isSpectrumSwitch" @change="isSpectrumSwitchChanged"></el-switch>
                    </el-form-item>
                    <el-form-item v-if="isSpectrumSwitch" label="频谱类型" class="bbbug_my_setting__form_spectrum_switch">
                      <el-radio-group v-model="isSpectrumSwitchType" @change="isSpectrumSwitchTypeChanged" size="mini">
                        <el-radio-button label="0">柱</el-radio-button>
                        <el-radio-button label="1">面</el-radio-button>
                        <el-radio-button label="2">线</el-radio-button>
                      </el-radio-group>
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
                    isEnableTouchNotice: true,
                    isEnableAtNotification: true,
                    isDarkModelTemp: false,
                    isSpectrumSwitch: false,
                    isSpectrumSwitchType: 0
                }
            },
            created() {
                this.userInfo = Object.assign({}, this.global.userInfo);
                this.roomInfo = Object.assign({}, this.global.roomInfo);
                this.isEnableNoticePlayer = localStorage.getItem('isEnableNoticePlayer') != 1 ? true : false;
                this.isEnableNotification = localStorage.getItem('isEnableNotification') != 1 ? true : false;
                this.isEnableTouchNotice = localStorage.getItem('isEnableTouchNotice') != 1 ? true : false;
                this.isEnableAtNotification = localStorage.getItem('isEnableAtNotification') != 1 ? true : false;
                this.isDarkModelTemp = this.$parent.isDarkModel;
                this.isSpectrumSwitch = localStorage.getItem('isSpectrumSwitch') != 1 ? true : false;
                this.isSpectrumSwitchType = localStorage.getItem('isSpectrumSwitchType');
            },
            methods: {
                /**
                 * @description: @通知切换事件
                 * @param {null}
                 * @return {null}
                 */
                isEnableAtNotificationChanged() {
                    localStorage.setItem('isEnableAtNotification', this.isEnableAtNotification ? 0 : 1);
                    this.$parent.loadConfig();
                },
                /**
                 * @description: 声音切换事件
                 * @param {null}
                 * @return {null}
                 */
                isEnableNoticePlayerChanged() {
                    localStorage.setItem('isEnableNoticePlayer', this.isEnableNoticePlayer ? 0 : 1);
                    this.$parent.loadConfig();
                },
                /**
                 * @description: 通知切换事件
                 * @param {null}
                 * @return {null}
                 */
                isEnableNotificationChanged() {
                    localStorage.setItem('isEnableNotification', this.isEnableNotification ? 0 : 1);
                    this.$parent.loadConfig();
                },
                /**
                 * @description: 摸一摸通知切换事件
                 * @param {null}
                 * @return {null}
                 */
                isEnableTouchNoticeChanged() {
                    localStorage.setItem('isEnableTouchNotice', this.isEnableTouchNotice ? 0 : 1);
                    this.$parent.loadConfig();
                },
                /**
                 * @description: 暗黑模式切换事件
                 * @param {null}
                 * @return {null}
                 */
                isDarkModelChanged() {
                    this.$parent.updateDarkModel(this.isDarkModelTemp);
                },
                /**
                 * @description: 频谱开关切换事件
                 * @param {null}
                 * @return {null}
                 */
                isSpectrumSwitchChanged() {
                  localStorage.setItem('isSpectrumSwitch', this.isSpectrumSwitch ? 0 : 1);
                  this.$parent.updateSpectrumSwitch(this.isSpectrumSwitch);
                },
              /**
               * @description: 频谱类型切换事件
               * @param {null}
               * @return {null}
               */
                isSpectrumSwitchTypeChanged(){
                  localStorage.setItem('isSpectrumSwitchType', this.isSpectrumSwitchType);
                  this.$parent.updateSpectrumSwitchType(this.isSpectrumSwitchType);
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

    .bbbug_my_setting__form_spectrum_switch .el-radio-button__inner{
        padding: 5px 10px;
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
