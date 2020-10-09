
const scrollFuncs = {

  /**
   * 滚动回底部
   * @param {Event} e 
   */
  scrollToBottom: function () {
    const el = document.querySelector('#chat_room_history');
    el.scrollTop = el.scrollHeight;
  },

  /**
   * chat_room_history 滚动事件
   * @param {Event} e 
   * @param {Vue} vue
   */
  "chat_room_history": function (e, vue) {
    const el = e.target;
    // 如果滚动条不在最底部则现实滚动到底部按钮
    if (el.scrollHeight - el.scrollTop - el.clientHeight > 100) {
      vue.showScrollToBottomBtn = true;
    } else {
      vue.showScrollToBottomBtn = false;
    }
  },

  /**
   * 滚动到指定消息
   * @param {*} msgid 
   */
  scrollToChat: function (msgid) {
    const chatRoom = document.querySelector('#chat_room_history');
    const chat = document.querySelector(`#msgid_${msgid}`);
    if (!chat) {
      Vue.prototype.$message.error("无法定位到该消息！");
      return;
    }
    chatRoom.scrollTop = chat.offsetTop - 100;
    chat.style.backgroundColor = 'rgba(251, 114, 153,0.1)';
    chat.style.transition = 'all 600ms';
    setTimeout(() => {
      chat.style.transition = 'all 2000ms';
      chat.style.backgroundColor = 'transparent';
      setTimeout(() => {
        chat.style.transition = 'unset';
      }, 2000);
    }, 3000);
  }



}