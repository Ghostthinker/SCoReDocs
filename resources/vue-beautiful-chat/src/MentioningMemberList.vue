<template>
  <div
    v-if="filteredParticipants.length > 0"
    ref="mentioningMemberList"
    class="sc-mentioning-member-list"
    @keydown="keydownList"
  >
    <transition-group name="slide-fade">
      <div
        v-for="(user, index) in filteredParticipants"
        :key="user.id"
        :tabindex="index"
        class="sc-mentioning-member-container"
        :class="{autoFocus: index === 0 && !focusOnMemberList}"
        @click="mentionMember(user)"
        @keyup.enter="mentionMember(user)"
      >
        <div class="sc-mentioning-member">
          <img v-if="user.id !== 0" alt="profile-img" :src="user.imageUrl" class="img-msg" />
          <label>{{ user.name }}</label>
        </div>
      </div>
    </transition-group>
  </div>
</template>

<script>
export default {
  name: 'MentioningMemberList',
  props: {
    participants: {
      type: Array,
      required: true
    },
    searchText: {
      type: String,
      required: true
    },
    focusOnMemberList: {
      type: Boolean,
      required: false,
      default: false
    },
    onSubmitFirstMentioningMember: {
      type: Boolean,
      required: false
    }
  },
  data() {
    return {
      focusIndex: 1
    }
  },
  computed: {
    filteredParticipants() {
      return [
        {
          id: 0,
          name: 'Alle',
          imageUrl: '',
          online: false,
          deleted: false,
          showUserInParticipantList: true
        },
        ...this.participants
      ].filter(
        (par) =>
          par.name.toLowerCase().includes(this.searchText.toLowerCase()) &&
          !par.deleted &&
          par.showUserInParticipantList
      )
    }
  },
  watch: {
    focusOnMemberList: function () {
      if (this.focusOnMemberList) {
        this.setCurrentFocus()
      }
    },
    filteredParticipants: function () {
      this.$emit('filteredParticipants', this.filteredParticipants)
    },
    onSubmitFirstMentioningMember: function () {
      if (this.onSubmitFirstMentioningMember) {
        this.mentionMember(this.filteredParticipants[0])
      }
    }
  },
  mounted() {
    //this.setCurrentFocus()
  },
  updated() {
    //this.setCurrentFocus()
  },
  created() {
    this.$emit('filteredParticipants', this.filteredParticipants)
  },
  methods: {
    mentionMember(user) {
      this.$emit('mentionMember', user)
    },
    keydownList(event) {
      if (event.keyCode === 38 && this.focusIndex > 0) {
        this.focusIndex--
      }
      if (event.keyCode === 40 && this.focusIndex + 1 < this.filteredParticipants.length) {
        this.focusIndex++
      }
      console.log(this.focusIndex)
      this.setCurrentFocus()
    },
    setCurrentFocus() {
      this.$refs.mentioningMemberList.children[0].children[this.focusIndex].focus()
    }
  }
}
</script>

<style scoped>
.autoFocus {
  background-color: #0077ff73;
}
.sc-mentioning-member-list {
  padding: 0.5em 1em 0 1em;
  background-color: #f4f7f9;
  max-height: 200px;
  overflow-y: auto;
  overflow-x: hidden;
  border-top-left-radius: 7px;
  border-top-right-radius: 7px;
  cursor: pointer;
  display: flex;
  flex-direction: column;
  flex-shrink: 0;
}

.sc-mentioning-member-container {
  border-bottom: 1px solid;
  border-color: #eaeaea;
  display: flex;
  align-items: center;
  height: 38px;
  flex-shrink: 0;
}
.sc-mentioning-member-container:focus {
  background-color: #0077ff73;
  outline: none;
}

.img-msg {
  border-radius: 50%;
  width: 20px;
  height: 20px;
}

.sc-mentioning-member {
  cursor: pointer;
  width: 100%;
}

label {
  margin-bottom: 0 !important;
  padding-left: 8px;
}

.slide-fade-enter-active {
  transition: all 0.4s ease;
}
.slide-fade-leave-active {
  transition: all 0.4s cubic-bezier(1, 0.5, 0.8, 1);
}
.slide-fade-enter,
.slide-fade-leave-to {
  transform: translateX(200px);
  opacity: 0.8;
}
</style>
