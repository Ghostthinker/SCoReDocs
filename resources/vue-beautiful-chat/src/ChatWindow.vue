<template>
  <div class="sc-chat-window" :class="{opened: isOpen, closed: !isOpen}">
    <Header
      :show-change-context-button="showChangeContextButton"
      :title="title"
      :image-url="titleImageUrl"
      :change-context="changeContext"
      :change-context-tooltip="changeContextTooltip"
      :colors="colors"
      :disable-user-list-toggle="disableUserListToggle"
      @userList="handleUserListToggle"
    >
      <template>
        <slot name="header"> </slot>
      </template>
    </Header>
    <UserList v-if="showUserList" :colors="colors" :participants="participants" />
    <MessageList
      v-if="!showUserList"
      :messages="messages"
      :participants="participants"
      :show-typing-indicator="showTypingIndicator"
      :typing-user-array="typingUserArray"
      :colors="colors"
      :always-scroll-to-bottom="alwaysScrollToBottom"
      :show-edition="showEdition"
      :show-deletion="showDeletion"
      :show-reply="showReply"
      :message-styling="messageStyling"
      :jump-to-message="jumpToMessage"
      @scrollToTop="$emit('scrollToTop')"
      @remove="$emit('remove', $event)"
      @reply="setReplyPreviewData"
    >
      <template v-slot:user-avatar="scopedProps">
        <slot name="user-avatar" :user="scopedProps.user" :message="scopedProps.message"> </slot>
      </template>
      <template v-slot:text-message-body="scopedProps">
        <slot
          name="text-message-body"
          :message="scopedProps.message"
          :messageText="scopedProps.messageText"
          :messageColors="scopedProps.messageColors"
          :me="scopedProps.me"
        >
        </slot>
      </template>
      <template v-slot:system-message-body="scopedProps">
        <slot name="system-message-body" :message="scopedProps.message"> </slot>
      </template>
      <template v-slot:text-message-toolbox="scopedProps">
        <slot name="text-message-toolbox" :message="scopedProps.message" :me="scopedProps.me">
        </slot>
      </template>
    </MessageList>
    <ReplyPreview
      v-if="showReplyPreview"
      :message="replyPreviewMessage"
      :author="replyPreviewAuthor"
      @closeReplyPreview="closeReplyPreview"
    >
    </ReplyPreview>
    <UserInput
      v-if="!showUserList"
      :show-emoji="showEmoji"
      :on-submit="onUserInputSubmit"
      :suggestions="getSuggestions()"
      :show-file="showFile"
      :placeholder="placeholder"
      :colors="colors"
      :message-parent="replyPreviewMessage"
      :participants="participants"
      @onType="$emit('onType', $event)"
      @edit="$emit('edit', $event)"
      @messageSend="onMessageSend()"
    />
  </div>
</template>

<script>
import Header from './Header.vue'
import MessageList from './MessageList.vue'
import UserInput from './UserInput.vue'
import UserList from './UserList.vue'
import ReplyPreview from './ReplyPreview.vue'

export default {
  components: {
    Header,
    MessageList,
    UserInput,
    UserList,
    ReplyPreview
  },
  props: {
    showEmoji: {
      type: Boolean,
      default: false
    },
    showChangeContextButton: {
      type: Boolean,
      default: true
    },
    changeContextTooltip: {
      type: String,
      default: 'Zum Projekt Chat'
    },
    showFile: {
      type: Boolean,
      default: false
    },
    participants: {
      type: Array,
      required: true
    },
    title: {
      type: String,
      required: true
    },
    titleImageUrl: {
      type: String,
      default: ''
    },
    onUserInputSubmit: {
      type: Function,
      required: true
    },
    changeContext: {
      type: Function,
      required: true
    },
    messageList: {
      type: Array,
      default: () => []
    },
    isOpen: {
      type: Boolean,
      default: () => false
    },
    placeholder: {
      type: String,
      default: 'Eine Nachricht schreiben...'
    },
    showTypingIndicator: {
      type: String,
      required: true
    },
    typingUserArray: {
      type: Array,
      required: true
    },
    colors: {
      type: Object,
      required: true
    },
    alwaysScrollToBottom: {
      type: Boolean,
      required: true
    },
    messageStyling: {
      type: Boolean,
      required: true
    },
    disableUserListToggle: {
      type: Boolean,
      default: false
    },
    showEdition: {
      type: Boolean,
      required: true
    },
    showDeletion: {
      type: Boolean,
      required: true
    },
    showReply: {
      type: Boolean,
      required: true
    },
    jumpToMessage: {
      type: Number,
      required: false
    }
  },
  data() {
    return {
      showUserList: false,
      replyPreviewMessage: null,
      replyPreviewAuthor: null
    }
  },
  computed: {
    messages() {
      return this.messageList
    },
    showReplyPreview() {
      return this.replyPreviewMessage && this.replyPreviewMessage
    }
  },
  methods: {
    handleUserListToggle(showUserList) {
      this.showUserList = showUserList
    },
    getSuggestions() {
      return this.messages.length > 0 ? this.messages[this.messages.length - 1].suggestions : []
    },
    setReplyPreviewData(data) {
      this.replyPreviewMessage = data.message
      this.replyPreviewAuthor = data.author
    },
    onMessageSend() {
      this.closeReplyPreview()
    },
    closeReplyPreview() {
      this.replyPreviewMessage = null
      this.replyPreviewAuthor = null
    }
  }
}
</script>

<style scoped>
.sc-chat-window {
  width: 370px;
  height: calc(100% - 120px);
  max-height: 590px;
  position: absolute;
  right: 25px;
  bottom: 100px;
  box-sizing: border-box;
  box-shadow: 0px 7px 7px 5px rgba(148, 149, 150, 0.1);
  background: white;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  border-radius: 10px;
  font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
  animation: fadeIn;
  animation-duration: 0.3s;
  animation-timing-function: ease-in-out;
  z-index: 10020;
  overflow: hidden;
}

.sc-chat-window.closed {
  opacity: 0;
  display: none;
  bottom: 90px;
}

@keyframes fadeIn {
  0% {
    display: none;
    opacity: 0;
  }

  100% {
    display: flex;
    opacity: 1;
  }
}

.sc-message--me {
  text-align: right;
}
.sc-message--them {
  text-align: left;
}

@media (max-width: 450px) {
  .sc-chat-window {
    width: 100%;
    height: 100%;
    max-height: 100%;
    right: 0px;
    bottom: 0px;
    border-radius: 0px;
  }
  .sc-chat-window {
    transition: 0.1s ease-in-out;
  }
  .sc-chat-window.closed {
    bottom: 0px;
  }
}
</style>

<style>
.online-indicator {
  width: 8px;
  height: 8px;
  border-radius: 4px;
  background-color: #9dff00;
  -webkit-box-shadow: 0px 0px 0px 2px rgba(112, 112, 112, 1);
  -moz-box-shadow: 0px 0px 0px 2px rgba(112, 112, 112, 1);
  box-shadow: 0px 0px 0px 2.5px rgba(112, 112, 112, 1);
}
</style>
