<template>
  <div id="SearchSongs">
    <div class="bbbug_main_right">
      <div class="bbbug_main_right_song">
        <div class="bbbug_main_right_song_title">
          歌曲搜索
          <!-- <router-link to="/search_songs" class="bbbug_main_right_song_right">我要点歌</router-link> -->
          <el-input style="margin-top: 20px" placeholder="歌名/歌手/专辑搜索" size="small" v-model="keyword" clearable
            @keydown.13.native="getList">
            <el-button slot="append" icon="el-icon-search" @click="getList">搜索</el-button>
          </el-input>
        </div>
        <div class="bbbug_main_right_song_list_search" v-loading="bbbug_loading">
          <div class="bbbug_scroll">
            <div class="bbbug_main_right_song_item" v-for="(item, index) in list" v-loading="item.loading">
              <div class="bbbug_main_right_song_name">
                {{item.name}}
              </div>
              <div class="bbbug_main_right_song_singer">
                歌手: {{item.singer}}
              </div>
              <div class="bbbug_main_right_song_controll">
                <button v-if="roomInfo.room_type==1 || roomInfo.room_type==4 && roomInfo.room_user!=userInfo.user_id"
                  class="bbbug_main_right_song_button bbbug_main_right_song_add" @click="addSong(index)">点歌</button>
                <button v-if="roomInfo.room_type==4 && roomInfo.room_user==userInfo.user_id"
                  class="bbbug_main_right_song_button bbbug_main_right_song_add" @click="playSong(index)">播放</button>
              </div>
            </div>
          </div>
          <div class="bbbug_tips" v-if="list.length==0">输入精准词可快速搜索</div>

          <div v-if="atSongUserInfo" class="bbbug_search_song_user">
            <div class="bbbug_search_song_user_box">
              <img class="bbbug_search_song_head" :src="atSongUserInfo.user_head"
                onerror="this.src='//cdn.bbbug.com/new/images/nohead.jpg'" />
              <div class="bbbug_search_song_name">{{urldecode(atSongUserInfo.user_name)}}</div>
              <div class="bbbug_search_song_tips">你正在为Ta选一首歌送出</div>
              <div class="bbbug_search_song_cancel" @click="clearUser">取消</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
  export default {
    data() {
      return {
        bbbug_loading: false,
        list: [],
        keyword: "",
        userInfo: false,
        roomInfo: false,
        atSongUserInfo: false,
      };
    },
    created() {
      this.userInfo = this.global.userInfo;
      this.roomInfo = Object.assign({}, this.global.roomInfo);
      this.atSongUserInfo = this.global.atSongUserInfo;
      if(this.global.songKeyword){
        this.keyword = this.global.songKeyword;
        this.getList();
      }
    },
    methods: {
      clearUser() {
        let that = this;
        that.atSongUserInfo = false;
        that.global.atSongUserInfo = false;
      },
      getList() {
        let that = this;
        that.bbbug_loading = true;
        that.request({
          url: "song/search",
          data: {
            room_id: that.global.room_id,
            keyword: that.keyword,
          },
          success(res) {
            that.bbbug_loading = false;
            that.list = res.data;
          },
          error(res) {
            that.bbbug_loading = false;
          }
        });
      },
      addSong(index) {
        let that = this;
        if (that.list[index].loading) {
          return;
        }
        that.list[index].loading = true;
        that.$forceUpdate();
        that.request({
          url: "song/addSong",
          data: {
            room_id: that.global.room_id,
            mid: that.list[index].mid,
            at: that.atSongUserInfo ? that.atSongUserInfo.user_id : null
          },
          success(res) {
            that.$message.success(res.msg);
            that.list[index].loading = false;
            that.clearUser();
            that.$forceUpdate();
          },
          error(res) {
            that.list[index].loading = false;
            that.$forceUpdate();
          }
        });
      },
      playSong(index) {
        let that = this;
        if (that.list[index].loading) {
          return;
        }
        that.list[index].loading = true;
        that.$forceUpdate();
        that.request({
          url: "song/playSong",
          data: {
            room_id: that.global.room_id,
            mid: that.list[index].mid
          },
          success(res) {
            that.$message.success(res.msg);
            that.list[index].loading = false;
            that.$forceUpdate();
          },
          error(res) {
            that.list[index].loading = false;
            that.$forceUpdate();
          }
        });
      }
    },
  };
</script>
<style>
  .bbbug_search_song_user {
    position: absolute;
    left: 0;
    right: 0;
    bottom: 20px;
    text-align: center;
  }

  .bbbug_search_song_user_box {
    width: 300px;
    display: inline-block;
    position: relative;
    height: 60px;
    background: #fff;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.5);
    border-radius: 10px;
  }

  .bbbug_search_song_head {
    width: 50px;
    height: 50px;
    border-radius: 100%;
    border: 1px solid #eee;
    position: absolute;
    left: 5px;
    top: 5px;
  }

  .bbbug_search_song_name {
    font-size: 14px;
    color: #333;
    position: absolute;
    left: 65px;
    top: 5px;
    font-weight: bold;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    width: 190px;
    text-align: left;
  }

  .bbbug_search_song_tips {
    font-size: 12px;
    color: #999;
    position: absolute;
    left: 65px;
    top: 35px;
    text-align: left;
  }

  .bbbug_search_song_cancel {
    background-color: #eee;
    position: absolute;
    right: 5px;
    top: 5px;
    font-size: 12px;
    color: #666;
    padding: 3px 10px;
    border-radius: 5px;
    cursor: pointer;
  }

  .bbbug_search_song_cancel:active {
    background-color: #ccc;
  }
</style>