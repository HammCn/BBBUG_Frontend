//main.js文件中引入
import Vue from 'vue';
import VueRouter from 'vue-router';
//导入ElementUI
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
Vue.use(ElementUI);
import axios from 'axios';
import './assets/css/bbbug.css';
Vue.prototype.$axios = axios;
import clipboard from 'clipboard';
//注册到vue原型上
Vue.prototype.clipboard = clipboard;

Vue.prototype.Event = new Vue();
Vue.prototype.global = {
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
    },
};
Vue.prototype.doLogout = function () {
    this.global.userInfo = this.global.guestUserInfo;
    this.global.baseData.access_token = this.global.guestUserInfo.access_token;
    localStorage.removeItem('access_token');
};

Vue.prototype.urldecode = function (str) {
    return decodeURIComponent(str);
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
                            that.doLogout();
                            that.$router.push('/login');
                        }).catch(function () {
                            if (that.global.baseData.access_token != that.global.guestUserInfo.access_token) {
                                that.doLogout();
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
import preview from 'vue-photo-preview'
import 'vue-photo-preview/dist/skin.css'
import RoomList from './components/RoomList.vue';
import MySongList from './components/MySongList.vue';
import PlayingSongList from './components/PlayingSongList.vue';
import SearchSongs from './components/SearchSongs.vue';
import Login from './components/Login.vue';
import GiteeCallback from './components/GiteeCallback.vue';
import OSChinaCallback from './components/OSChinaCallback.vue';
import DingCallback from './components/DingCallback.vue';
import QQCallback from './components/QQCallback.vue';
// import GithubCallback from './components/GithubCallback.vue';
import OnlineList from './components/OnlineList.vue';
import RoomSetting from './components/RoomSetting.vue';
import RoomCreate from './components/RoomCreate.vue';
import RoomPassword from './components/RoomPassword.vue';
import MySetting from './components/MySetting.vue';
import AutoLogin from './components/AutoLogin.vue';
import Profile from './components/Profile.vue';

Vue.use(vuePhotoPreview, {});
//安装插件
Vue.use(VueRouter); //挂载属性
//创建路由对象并配置路由规则
let router = new VueRouter({
    mode: 'history',
    routes: [
        //一个个对象
        {
            path: '/login',
            component: Login
        }, {
            path: '/hot_rooms',
            component: RoomList
        }, {
            path: '/my_songs',
            component: MySongList
        }, {
            path: '/playing_songs',
            component: PlayingSongList
        }, {
            path: '/search_songs',
            component: SearchSongs
        }, {
            path: '/gitee',
            component: GiteeCallback
        }, {
            path: '/oschina',
            component: OSChinaCallback
        }, {
            path: '/qq',
            component: QQCallback
        }, {
            path: '/ding',
            component: DingCallback
        }, {
            path: '/online',
            component: OnlineList
        }, {
            path: '/room_setting',
            component: RoomSetting
        }, {
            path: '/room_password',
            component: RoomPassword
        }, {
            path: '/create_room',
            component: RoomCreate
        }, {
            path: '/my_setting',
            component: MySetting
        }, {
            path: '/auto_login',
            component: AutoLogin
        }, {
            path: '/*.html',
            component: AutoLogin
        }, {
            path: '/profile',
            component: Profile
        },
        // {
        //     path: '/github',
        //     component: GithubCallback
        // }
    ]
});
//new Vue 启动
new Vue({
    el: '#app',
    //让vue知道我们的路由规则
    router: router, //可以简写router
    render: c => c(App),
});