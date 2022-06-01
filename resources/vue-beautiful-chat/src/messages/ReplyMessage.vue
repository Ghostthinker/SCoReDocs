<template>
  <div class="sc-reply-preview" :style="cssProps">
    <div class="sc-reply-preview-message-text"  :class="isClientMessage ? 'sc-reply-preview-message-text-client-message' : 'sc-reply-preview-message-text-other'" >
      <span :class="isClientMessage ? 'sc-reply-preview-message-author-client-message' : 'sc-reply-preview-message-author-other'">{{ author }}</span>
      <v-tooltip left open-delay="350" max-width="250px">
        <template v-slot:activator="{ on, attrs }">
          <div
            v-on="on"
            v-bind="attrs"
          >
            <div v-html="messageTextShorted"></div>
          </div>
        </template>
          <div v-html="messageTextTooltip"></div>
      </v-tooltip>
    </div>
  </div>
</template>

<script>
import escapeGoat from 'escape-goat'
import Autolinker from 'autolinker'
const fmt = require('msgdown')

export default {
  name: 'ReplyMessage',
  props: {
    message: {
      type: Object,
      required: true
    },
    author: {
      type: String,
      required: true
    },
    isClientMessage: {
      type: Boolean,
      required: true
    },
    messageColors: {
      type: Object,
      required: true
    },
  },
  computed: {
    messageText() {
      const escaped = escapeGoat.escape(this.message.data.text)

      return Autolinker.link(this.messageStyling ? fmt(escaped) : escaped, {
        className: 'chatLink',
        truncate: {length: 50, location: 'smart'}
      })
    },
    messageTextShorted() {
      if (this.messageText.length > 40) {
        return this.messageText.substring(0, 39) + '...'
      }
      return this.messageText
    },
    messageTextTooltip() {
      if (this.messageText.length > 500) {
        return this.messageText.substring(0, 499) + '...'
      }
      return this.messageText
    },
    cssProps() {
      return {
        '--bg-color-reply-message' : this.messageColors.backgroundColor
      }
    }
  }
}
</script>

<style scoped lang="scss">

.sc-reply-preview {
  margin-bottom: 5px;
  padding: 0.5em 1em 0.5em 1em;
  filter: brightness(90%);
  background-color: var(--bg-color-reply-message);
  border-radius: 6px;
  cursor: pointer;
}

.sc-reply-preview-message-text {
  width: auto;
  padding: 8px 12px;
  border-left: 4px solid var(--v-primary-base);
  color: black
}

.sc-reply-preview-message-text-client-message {
  border-left: 4px solid white;
}

.sc-reply-preview-message-text-other {
  border-left: 4px solid var(--v-primary-base);
}

.sc-reply-preview-message-author-client-message {
  color: white;
}

.sc-reply-preview-message-author-other {
  color: var(--v-primary-base)
}

.sc-reply-preview-message-author-container{
  display: flex;
  justify-content: space-between;
  align-items: center;
}
</style>
