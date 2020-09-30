
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
  }

}