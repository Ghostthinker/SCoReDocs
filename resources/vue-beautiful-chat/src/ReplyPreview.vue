<template>
  <div class="sc-reply-preview">
    <div class="sc-reply-preview-message-text" >
      <div class="sc-reply-preview-message-author-container">
        <span class="sc-reply-preview-message-author">{{ author.name }}</span>
        <v-btn icon>
          <v-icon @click="$emit('closeReplyPreview')">mdi-close</v-icon>
        </v-btn>
      </div>
      <div v-html="messageTextShorted"></div>
    </div>
  </div>
</template>

<script>
import escapeGoat from 'escape-goat'
import Autolinker from 'autolinker'
const fmt = require('msgdown')

export default {
  name: 'ReplyPreview',
  props: {
    message: {
      type: Object,
      required: true
    },
    author: {
      type: Object,
      required: true
    }
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
      if (this.messageText.length > 200) {
        return this.messageText.substring(0, 199) + '...'
      }
      return this.messageText
    },
  }
}
</script>

<style scoped lang="scss">

.sc-reply-preview {
  padding: 0.5em 1em 0 1em;
  background-color: #f4f7f9;;
}

.sc-reply-preview-message-text {
  width: auto;
  padding: 8px 12px;
  border-left: 4px solid var(--v-primary-base);
  background-color: darken(#f4f7f9, 5%);
}

.sc-reply-preview-message-author {
  color: var(--v-primary-base)
}

.sc-reply-preview-message-author-container{
  display: flex;
  justify-content: space-between;
  align-items: center;
}
</style>
