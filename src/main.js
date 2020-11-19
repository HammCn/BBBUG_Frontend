//main.js文件中引入
import Vue from 'vue';
//导入ElementUI
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
Vue.use(ElementUI);
import axios from 'axios';
import preview from 'vue-photo-preview'
import 'vue-photo-preview/dist/skin.css'
import './assets/css/bbbug.css';
Vue.prototype.$axios = axios;
import clipboard from 'clipboard';
//注册到vue原型上
Vue.prototype.clipboard = clipboard;

Vue.prototype.Event = new Vue();
Vue.prototype.global = {
    isDarkModel: true,
    room_id: 888,
    room_password: "",
    guestUserInfo: {
        myRoom: false,
        user_admin: false,
        user_head: "//cdn.bbbug.com/new/images/nohead.jpg",
        user_id: -1,
        user_name: "Ghost",
        access_token: "45af3cfe44942c956e026d5fd58f0feffbd3a237",
    },
    userInfo: false,
    roomInfo: false,
    atUserInfo: false,
    atSongUserInfo: false,
    profileUserId: false,
    baseData: {
        access_token: '',
        plat: 'vue',
        version: 10000,
    },
    api: {
        url: "https://api.bbbug.com/api/",
        static: "https://cdn.bbbug.com/",//这里修改为 https://api.bbbug.com/ 对应你的api 根路径
    },
    songKeyword: "",
};

Vue.prototype.isDarkModel = false;
Vue.prototype.changeDarkModel = function (enabled) {
    this.isDarkModel = enabled;
};
Vue.prototype.urldecode = function (str) {
    return decodeURIComponent(str);
};

Vue.prototype.http2https = function (str) {
    return str.toString().replace("http://", "https://");
};

Vue.prototype.request = function (_data) {
    let that = this;
    axios.post(that.global.api.url + (_data.url || ""), Object.assign({}, that.global.baseData, _data.data || {}))
        .then(function (response) {
            switch (response.data.code) {
                case 200:
                    if (_data.success) {
                        _data.success(response.data);
                    } else {
                        that.$message.success(response.data.msg);
                    }
                    break;
                case 401:
                    that.$emit('App', 'hideLoading');
                    that.$emit('App', 'hideAll');
                    that.$parent.$emit('App', 'hideLoading');
                    that.$parent.$emit('App', 'hideAll');
                    console.log(that);
                    if (_data.login) {
                        that.$message.error(response.data.msg);
                        _data.login();
                    } else {
                        that.$confirm(response.data.msg, '无权访问', {
                            confirmButtonText: '登录',
                            cancelButtonText: '取消',
                            closeOnClickModal: false,
                            closeOnPressEscape: false,
                            type: 'warning'
                        }).then(function () {
                            that.$emit('App', 'loginGuest');
                            that.$emit('App', 'showLoginForm');
                            that.$parent.$emit('App', 'loginGuest');
                            that.$parent.$emit('App', 'showLoginForm');
                        }).catch(function () {
                            if (that.global.baseData.access_token != that.global.guestUserInfo.access_token) {
                                that.$parent.$emit('App', 'loginGuest');
                                that.request(_data);
                            }
                        });
                    }
                    break;
                default:
                    if (_data.error) {
                        that.$message.error(response.data.msg);
                        _data.error(response.data);
                    } else {
                        that.$message.error(response.data.msg);
                    }
            }
        })
        .catch(function (error) {
            console.log(error)
        });
};

Vue.prototype.callParentFunction = function (event, msg) {
    //触发父容器方法
    if (self != top) {
        window.parent.postMessage({
            'type': event,
            'msg': msg
        }, '*');
    }
};
Vue.prototype.websocket = {
    url: "",
    connection: null,
    isConnected: false,
    isForceStop: false,
    reConnectTimer: null,
    heartBeatTimer: null
};
//主体
import App from './App.vue';

Vue.use(vuePhotoPreview, {});

//new Vue 启动
new Vue({
    el: '#app',
    render: c => c(App),
});