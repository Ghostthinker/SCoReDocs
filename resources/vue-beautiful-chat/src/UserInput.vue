<template>
  <div>
    <transition name="slide-from-bottom">
      <MentioningMemberList
        v-if="isMentioning"
        :search-text="mentioningText"
        :participants="participants"
        :focus-on-member-list="focusOnMemberList"
        :on-submit-first-mentioning-member="submitFirstMentioningMember"
        @mentionMember="_mentionMember"
        @filteredParticipants="setFilteredParticipants"
      >
      </MentioningMemberList>
    </transition>
    <Suggestions :suggestions="suggestions" :colors="colors" @sendSuggestion="_submitSuggestion" />
    <div
      v-if="file"
      class="file-container"
      :style="{
        backgroundColor: colors.userInput.text,
        color: colors.userInput.bg
      }"
    >
      <span class="icon-file-message"
        ><img :src="icons.file.img" :alt="icons.file.name" height="15"
      /></span>
      {{ file.name }}
      <span class="delete-file-message" @click="cancelFile()"
        ><img
          :src="icons.closeSvg.img"
          :alt="icons.closeSvg.name"
          height="10"
          title="Remove the file"
      /></span>
    </div>
    <form
      class="sc-user-input"
      :class="{active: inputActive}"
      :style="{background: colors.userInput.bg}"
    >
      <div
        ref="userInput"
        role="button"
        tabIndex="0"
        contentEditable="true"
        :placeholder="placeholder"
        class="sc-user-input--text"
        :style="{color: colors.userInput.text}"
        @focus="setInputActive(true)"
        @blur="setInputActive(false)"
        @keydown="handleKey"
        @keyup="handleKeyUp"
        @focusUserInput="focusUserInput()"
        @click="
          isMentioning = false
          focusOnMemberList = false
        "
      ></div>
      <div class="sc-user-input--buttons">
        <div class="sc-user-input--button"></div>
        <div v-if="showEmoji && !isEditing" class="sc-user-input--button">
          <EmojiIcon :on-emoji-picked="_handleEmojiPicked" :color="colors.userInput.text" />
        </div>
        <div v-if="showFile && !isEditing" class="sc-user-input--button">
          <FileIcons :on-change="_handleFileSubmit" :color="colors.userInput.text" />
        </div>
        <div v-if="isEditing" class="sc-user-input--button">
          <UserInputButton
            :color="colors.userInput.text"
            tooltip="cancel"
            @click.native.prevent="_editFinish"
          >
            <IconCross />
          </UserInputButton>
        </div>
        <div class="sc-user-input--button">
          <UserInputButton
            v-if="isEditing"
            :color="colors.userInput.text"
            tooltip="Edit"
            @click.native.prevent="_editText"
          >
            <IconOk />
          </UserInputButton>
          <UserInputButton
            v-else
            :color="colors.userInput.text"
            tooltip="Send"
            @click.native.prevent="_submitText"
          >
            <IconSend />
          </UserInputButton>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import EmojiIcon from './icons/EmojiIcon.vue'
import FileIcons from './icons/FileIcons.vue'
import UserInputButton from './UserInputButton.vue'
import Suggestions from './Suggestions.vue'
import FileIcon from './assets/file.svg'
import CloseIconSvg from './assets/close.svg'
import store from './store/'
import IconCross from './components/icons/IconCross.vue'
import IconOk from './components/icons/IconOk.vue'
import IconSend from './components/icons/IconSend.vue'
import MentioningMemberList from './MentioningMemberList'

export default {
  components: {
    EmojiIcon,
    FileIcons,
    UserInputButton,
    Suggestions,
    IconCross,
    IconOk,
    IconSend,
    MentioningMemberList
  },
  props: {
    icons: {
      type: Object,
      default: function () {
        return {
          file: {
            img: FileIcon,
            name: 'default'
          },
          closeSvg: {
            img: CloseIconSvg,
            name: 'default'
          }
        }
      }
    },
    showEmoji: {
      type: Boolean,
      default: () => false
    },
    suggestions: {
      type: Array,
      default: () => []
    },
    showFile: {
      type: Boolean,
      default: () => false
    },
    onSubmit: {
      type: Function,
      required: true
    },
    placeholder: {
      type: String,
      default: 'Write something...'
    },
    colors: {
      type: Object,
      required: true
    },
    messageParent: {
      type: Object,
      required: false
    },
    participants: {
      type: Array,
      required: true
    }
  },
  data() {
    return {
      file: null,
      inputActive: false,
      store,
      doNotResetInputFlag: false,
      isMentioning: false,
      mentioningText: '',
      metioningsArray: [],
      atAllMentioning: false,
      currCursorPosition: null,
      focusOnMemberList: false,
      submitFirstMentioningMember: false,
      filteredParticipants: []
    }
  },
  computed: {
    editMessageId() {
      return this.isEditing && store.editMessage.id
    },
    isEditing() {
      return store.editMessage && store.editMessage.id
    }
  },
  watch: {
    editMessageId(m) {
      if (store.editMessage != null && store.editMessage != undefined) {
        this.$refs.userInput.focus()
        this.$refs.userInput.textContent = store.editMessage.data.text
      } else {
        this.$refs.userInput.textContent = ''
      }
    }
  },
  mounted() {
    this.$root.$on('focusUserInput', () => {
      if (this.$refs.userInput) {
        this.focusUserInput()
      }
    })
  },
  methods: {
    cancelFile() {
      this.file = null
    },
    setInputActive(onoff) {
      this.inputActive = onoff
    },
    handleKey(event) {
      if (event.keyCode === 13 && !event.shiftKey && this.filteredParticipants.length < 1) {
        if (!this.isEditing) {
          this._submitText(event)
        } else {
          this._editText(event)
        }
        this._editFinish()
        event.preventDefault()
      } else if (event.keyCode === 13 && this.filteredParticipants.length > 0) {
        this.submitFirstMentioningMember = true
        event.preventDefault()
      } else if (event.keyCode === 27) {
        this._editFinish()
        event.preventDefault()
      } else if (event.keyCode === 40) {
        this.focusOnMemberList = true
        event.preventDefault()
      }
      this.$emit('onType', this.$refs.userInput.textContent)
    },
    handleKeyUp(event) {
      this.checkMentioning(event)
    },
    _onStartMentioning(searchedName) {
      this.mentioningText = searchedName
      this.isMentioning = true
    },
    _onEndMentioning() {
      this.mentioningText = ''
      this.isMentioning = false
      this.focusOnMemberList = false
    },
    _mentionMember(user) {
      console.log(user)
      this.isMentioning = false
      this.focusOnMemberList = false
      this.submitFirstMentioningMember = false
      const text = this.$refs.userInput.textContent

      const textBeforeCursor = text.slice(0, this.currCursorPosition)
      const atIndex = textBeforeCursor.lastIndexOf('@')
      this.$refs.userInput.textContent =
        text.substring(0, atIndex) + '@' + user.name + text.substring(this.currCursorPosition)
      this.currCursorPosition = atIndex + 1 + user.name.length
      this.$refs.userInput.focus()

      this._setCaret(this.currCursorPosition)
    },
    checkMentioning(event) {
      this.currCursorPosition = this._getCaretPosition()
      const text = this.$refs.userInput.textContent
      const textBeforeCursor = text.slice(0, this.currCursorPosition)
      const searchedNames = textBeforeCursor.split('@')
      if (searchedNames.length > 1) {
        const searchedName = searchedNames[searchedNames.length - 1]
        const atIndex = textBeforeCursor.lastIndexOf('@')
        // at sign is at beginning of text or has a blank before
        if (atIndex === 0 || textBeforeCursor[atIndex - 1] === ' ') {
          this.isMentioning = true
          this._onStartMentioning(searchedName)
        } else if (this.isMentioning) {
          this.isMentioning = false
          this.focusOnMemberList = false
          this._onEndMentioning()
        }
      } else if (this.isMentioning) {
        this.isMentioning = false
        this.focusOnMemberList = false
        this._onEndMentioning()
      }
    },
    focusUserInput() {
      this.$nextTick(() => {
        this.$refs.userInput.focus()
      })
    },
    _submitSuggestion(suggestion) {
      this.onSubmit({author: 'me', type: 'text', data: {text: suggestion}})
    },
    _checkSubmitSuccess(success) {
      if (Promise !== undefined) {
        Promise.resolve(success).then(
          function (wasSuccessful) {
            if (wasSuccessful === undefined || wasSuccessful) {
              this.file = null
              this.resetInput()
            }
          }.bind(this)
        )
      } else {
        this.file = null
        this.resetInput()
      }
    },
    resetInput() {
      if (!this.doNotResetInputFlag) {
        this.$refs.userInput.innerHTML = ''
      }
      this.doNotResetInputFlag = false
    },
    onChange(value) {
      console.log(value)
    },
    _buildMentionings(text) {
      this.participants.forEach((part) => {
        if (text.endsWith('@' + part.name)) {
          this.metioningsArray.push(part.id)
          text = text.replace(new RegExp('@' + part.name + '$'), '[[user:' + part.id + ']]')
        }
        if (text.includes('@' + part.name + ' ')) {
          this.metioningsArray.push(part.id)
          text = text.replaceAll('@' + part.name + ' ', '[[user:' + part.id + ']] ')
        }
      })
      if (text.endsWith('@Alle')) {
        this.atAllMentioning = true
      }
      if (text.includes('@Alle ')) {
        this.atAllMentioning = true
      }
      return text
    },
    _submitText(event) {
      let text = this.$refs.userInput.textContent
      const file = this.file
      if (file) {
        this._submitTextWhenFile(event, text, file)
      } else {
        if (text && text.length > 0) {
          text = this._buildMentionings(text)
          this._checkSubmitSuccess(
            this.onSubmit({
              author: 'me',
              type: 'text',
              data: {text},
              parent_id: this.messageParent ? this.messageParent.id : null,
              mentionings: [...new Set(this.metioningsArray)],
              atAllMentioning: this.atAllMentioning
            })
          )
        }
      }
      this.metioningsArray = []
      this.atAllMentioning = false
      this.$emit('messageSend')
    },
    _submitTextWhenFile(event, text, file) {
      if (text && text.length > 0) {
        this._checkSubmitSuccess(
          this.onSubmit({
            author: 'me',
            type: 'file',
            data: {text, file}
          })
        )
      } else {
        this._checkSubmitSuccess(
          this.onSubmit({
            author: 'me',
            type: 'file',
            data: {file}
          })
        )
      }
    },
    _editText(event) {
      const text = this.$refs.userInput.textContent
      if (text && text.length) {
        this.$emit('edit', {
          author: 'me',
          type: 'text',
          id: store.editMessage.id,
          data: {text}
        })
        this._editFinish()
      }
    },
    _handleEmojiPicked(emoji) {
      this.doNotResetInputFlag = true

      this._checkSubmitSuccess(
        this.onSubmit({
          author: 'me',
          type: 'emoji',
          data: {emoji}
        })
      )
    },
    _handleFileSubmit(file) {
      this.file = file
    },
    _editFinish() {
      this.store.editMessage = null
    },
    _getCaretPosition() {
      const editableDiv = this.$refs.userInput
      let caretPos = 0
      let sel
      let range
      if (window.getSelection) {
        sel = window.getSelection()
        if (sel.rangeCount) {
          range = sel.getRangeAt(0)
          if (range.commonAncestorContainer.parentNode === editableDiv) {
            caretPos = range.endOffset
          }
        }
      } else if (document.selection && document.selection.createRange) {
        range = document.selection.createRange()
        if (range.parentElement() === editableDiv) {
          let tempEl = document.createElement('span')
          editableDiv.insertBefore(tempEl, editableDiv.firstChild)
          let tempRange = range.duplicate()
          tempRange.moveToElementText(tempEl)
          tempRange.setEndPoint('EndToEnd', range)
          caretPos = tempRange.text.length
        }
      }
      return caretPos
    },
    _setCaret(position) {
      const editableDiv = this.$refs.userInput
      let range = document.createRange()
      let sel = window.getSelection()

      range.setStart(editableDiv.childNodes[0], position)
      range.collapse(true)

      sel.removeAllRanges()
      sel.addRange(range)
    },
    setFilteredParticipants(filteredParticipants) {
      this.filteredParticipants = filteredParticipants
    }
  }
}
</script>

<style>
.sc-user-input {
  min-height: 55px;
  margin: 0px;
  position: relative;
  bottom: 0;
  display: flex;
  background-color: #f4f7f9;
  border-bottom-left-radius: 10px;
  border-bottom-right-radius: 10px;
  transition: background-color 0.2s ease, box-shadow 0.2s ease;
}

.sc-user-input--text {
  width: 300px;
  resize: none;
  border: none;
  outline: none;
  border-bottom-left-radius: 10px;
  box-sizing: border-box;
  padding: 18px;
  font-size: 15px;
  font-weight: 400;
  line-height: 1.33;
  white-space: pre-wrap;
  word-wrap: break-word;
  color: #565867;
  -webkit-font-smoothing: antialiased;
  max-height: 200px;
  overflow: scroll;
  bottom: 0;
  overflow-x: hidden;
  overflow-y: auto;
}

.sc-user-input--text:empty:before {
  content: attr(placeholder);
  display: block; /* For Firefox */
  /* color: rgba(86, 88, 103, 0.3); */
  filter: contrast(15%);
  outline: none;
  cursor: text;
}

.sc-user-input--buttons {
  width: 100px;
  position: absolute;
  right: 30px;
  height: 100%;
  display: flex;
  justify-content: flex-end;
}

.sc-user-input--button:first-of-type {
  width: 40px;
}

.sc-user-input--button {
  width: 30px;
  height: 55px;
  margin-left: 2px;
  margin-right: 2px;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.sc-user-input.active {
  box-shadow: none;
  background-color: white;
  box-shadow: 0px -5px 20px 0px rgba(150, 165, 190, 0.2);
}

.sc-user-input--button label {
  position: relative;
  height: 24px;
  padding-left: 3px;
  cursor: pointer;
}

.sc-user-input--button label:hover path {
  fill: rgba(86, 88, 103, 1);
}

.sc-user-input--button input {
  position: absolute;
  left: 0;
  top: 0;
  width: 100%;
  z-index: 99999;
  height: 100%;
  opacity: 0;
  cursor: pointer;
  overflow: hidden;
}

.file-container {
  background-color: #f4f7f9;
  border-top-left-radius: 10px;
  padding: 5px 20px;
  color: #565867;
}

.delete-file-message {
  font-style: normal;
  float: right;
  cursor: pointer;
  color: #c8cad0;
}

.delete-file-message:hover {
  color: #5d5e6d;
}

.icon-file-message {
  margin-right: 5px;
}

.slide-from-bottom-enter-active {
  transition: all 0.4s ease;
}
.slide-from-bottom-leave-active {
  transition: all 0.4s cubic-bezier(1, 0.5, 0.8, 1);
}
.slide-from-bottom-enter,
.slide-from-bottom-leave-to {
  transform: translateY(200px);
  opacity: 0.8;
}
</style>
