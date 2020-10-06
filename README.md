<p align="left">
<h1>BBBUG聊天室</h1>
</p>
<p align="left">
<a href="https://gitee.com/bbbug_com/ChatWEB/stargazers" target="_blank"><img src="https://svg.hamm.cn/gitee.svg?type=star&user=bbbug_com&project=ChatWEB"/></a>
<a href="https://gitee.com/bbbug_com/ChatWEB/members" target="_blank"><img src="https://svg.hamm.cn/gitee.svg?type=fork&user=bbbug_com&project=ChatWEB"/></a>
<img src="https://svg.hamm.cn/badge.svg?key=Base&value=Vue.Element"/>
<img src="https://svg.hamm.cn/badge.svg?key=License&value=GPL-3.0"/>
</p>

### 介绍

一个集音乐/聊天/看直播等一体的在线聊天室，支持了Gitee/OSChina/QQ等OAuth登录，支持多房间和创建私人房间，支持房间加密和切换房间模式，支持绑定二级域名与顶级域名，提供了第三方网站的快速接入方案。此仓库为PC站前端仓库。开发者QQ群：1140258698

体验一下：<a href="https://www.bbbug.com/" target="_blank">www.bbbug.com</a>

### 免责声明

平台音乐和视频直播流数据来源于第三方网站，仅供学习交流使用，请勿用于商业用途。

### 技术架构

IM后端采用 Node 实现 ```Websocket``` 服务，```Nginx``` 做Wss代理，前端采用 ```ElementUI&vue``` 实现，后端使用 ```StartAdmin``` 做管理平台。 Websocket.js 为后端Websocket实现代码，可自行安装相关包后使用pm2等进程管理工具将后端websocket持久化运行。

### 使用说明

clone当前项目，创建php运行环境(简单的项目初始化采用了php)，部署即可访问。

### 特色功能
```
1、创建房间、切换房间，房间权限与房间类型管理
2、点歌/切歌/猜歌/看直播/听歌与歌曲播放进度同步
```
### 感谢(以下排名不分先后)
``` 
Vue.js / ElementUI 
```

### 参与贡献
```
1. Fork 本仓库
2. 新建分支 添加或修改功能
3. 提交代码
4. 新建 Pull Request
```
### 贡献名单
[@Hamm](https://gitee.com/hamm)
[@kiripa](https://gitee.com/kiripa)
[查看更多](https://gitee.com/bbbug_com/ChatWEB/contributors?ref=master)

### 晒个截图
![BBBUG](https://images.gitee.com/uploads/images/2020/1006/174921_be0cca24_145025.png "截屏2020-10-06下午5.48.15.png")