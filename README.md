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

一个可以聊天听歌的音乐聊天室，支持了Gitee/OSChina/QQ/钉钉等OAuth登录，支持多房间和创建私人房间，支持房间加密和切换房间模式，支持绑定二级域名与顶级域名，提供了第三方网站的快速接入方案。此仓库为PC站前端仓库。开发者QQ群：1140258698

体验一下：<a href="https://www.bbbug.com/" target="_blank">www.bbbug.com</a>

### 免责声明

平台音乐和视频直播流数据来源于第三方网站，仅供学习交流使用，请勿用于商业用途。

### 技术架构

IM后端采用```node-websocket```实现```Websocket```服务，使用```pm2```持久化运行，```Nginx```做Wss代理，前端采用```ElementUI&vue```实现，后端使用```PHP```做数据接口API，```PHP-CLI```做后端数据同步，```Redis```做数据队列与缓存。 


### 使用说明

1. clone当前项目 ```git clone https://gitee.com/bbbug_com/ChatWEB.git```

2. 安装依赖项 ```npm install```

3. 开发环境运行```npm run dev``` 即可预览项目

4. 打包部署生产```npm run build```



### 已实现功能
```
1、普通文字与图片表情消息聊天功能
2、歌曲搜索、点歌、切歌、顶歌、收藏歌曲等功能
3、歌曲实时同步播放给房间所有人、支持房主电台模式
4、可创建房间、房主可禁言或禁止房间用户点歌
5、修改个人资料与设置等
6、ESC快捷沉浸式听歌体验
7、支持设置房间二级域名与绑定独立域名等

更多功能等你来扩展开发...
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

<p align="center">
<img src="https://images.gitee.com/uploads/images/2020/1111/224304_865849d4_145025.png" width="20%"/>
<img src="https://images.gitee.com/uploads/images/2020/1111/224508_98a510ad_145025.png" width="20%"/>
<img src="https://images.gitee.com/uploads/images/2020/1111/224725_e498aecc_145025.png" width="20%"/>
<img src="https://images.gitee.com/uploads/images/2020/1111/224817_294b2808_145025.png" width="20%"/>
</p>
