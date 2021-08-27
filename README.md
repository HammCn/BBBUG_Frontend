
<h1 align="center">BBBUG聊天室 前端仓库</h1>

<p align="center">
<a href="https://github.com/HammCn/BBBUG_Frontend" target="_blank">Github</a> | 
<a href="https://gitee.com/bbbug_com/ChatWEB" target="_blank">Gitee</a>
</p>

<p align="center">
<a href="https://gitee.com/bbbug_com/ChatWEB/stargazers" target="_blank"><img src="https://svg.hamm.cn/gitee.svg?type=star&user=bbbug_com&project=ChatWEB"/></a>
<a href="https://gitee.com/bbbug_com/ChatWEB/members" target="_blank"><img src="https://svg.hamm.cn/gitee.svg?type=fork&user=bbbug_com&project=ChatWEB"/></a>
<img src="https://svg.hamm.cn/badge.svg?key=Base&value=Vue.Element"/>
<img src="https://svg.hamm.cn/badge.svg?key=License&value=GPL-3.0"/>
</p>


<p align="center">
<a href="https://bbbug.com" target="_blank"><img src="https://api.bbbug.com/api/badge/888"/></a>
</p>

### 介绍

一个可以聊天听歌的音乐聊天室，支持了Gitee/OSChina/QQ/钉钉等OAuth登录，支持多房间和创建私人房间，支持房间加密和切换房间模式。此仓库为PC站前端仓库。QQ群1140258698

体验一下：<a href="https://demo.bbbug.com/" target="_blank">demo.bbbug.com</a>

### 免责声明

平台音乐数据来源于第三方网站，仅供学习交流使用，请勿用于商业用途。

### 技术架构

IM后端采用```node-websocket```实现```Websocket```服务，使用```pm2```持久化运行，```Nginx```做Wss代理，前端采用```ElementUI&vue```实现，后端使用```PHP```做数据接口API，```PHP-CLI```做后端数据同步，```Redis```做数据队列与缓存。 


### 使用说明

1. clone当前项目 ```git clone https://gitee.com/bbbug_com/ChatWEB.git```

2. 安装依赖项 ```npm install```

3. 开发环境运行```npm run dev``` 即可预览项目

4. 打包部署生产```npm run build```

[更详细的部署手册请查看这里>>>](https://doc.bbbug.com/3097561.html)


### 已实现功能
```
1、普通文字与图片表情消息聊天功能
2、歌曲搜索、点歌、切歌、顶歌、收藏歌曲等功能
3、歌曲实时同步播放给房间所有人、支持房主电台模式
4、可创建房间、房主可禁言或禁止房间用户点歌
5、修改个人资料与设置等
6、ESC快捷沉浸式听歌体验，聊天框支持Ctrl+Enter快速歌曲搜索
7、支持设置房间二级域名与绑定独立域名等
8、支持白天模式与暗黑模式两种主题，可自由设置
9、“摸一摸”等互动玩法

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

普通模式：

<p align="center">
<img src="https://images.gitee.com/uploads/images/2020/1111/224304_865849d4_145025.png" width="20%"/>
<img src="https://images.gitee.com/uploads/images/2020/1111/224508_98a510ad_145025.png" width="20%"/>
<img src="https://images.gitee.com/uploads/images/2020/1111/224725_e498aecc_145025.png" width="20%"/>
<img src="https://images.gitee.com/uploads/images/2020/1111/224817_294b2808_145025.png" width="20%"/>
</p>

暗黑模式：

<p align="center">
<img src="https://images.gitee.com/uploads/images/2020/1117/210841_ebaba0d3_145025.png" width="20%"/>
<img src="https://images.gitee.com/uploads/images/2020/1117/210853_1a151a42_145025.png" width="20%"/>
<img src="https://images.gitee.com/uploads/images/2020/1117/210911_bff2efec_145025.png" width="20%"/>
<img src="https://images.gitee.com/uploads/images/2020/1117/210945_564a2503_145025.png" width="20%"/>
</p>