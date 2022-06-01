<template>
    <v-alert
        color="#00a58d"
        prominent
        style="cursor: pointer;"
    >

        <v-row align="center">
            <v-col class="grow">
                <b class="mentioning-alert-head" @click="openChat">Neue Erwähnung!</b>
            </v-col>
            <v-col class="shrink icon-col">
                <v-icon class="icon" @click="close">mdi-close</v-icon>
            </v-col>
        </v-row>
        <strong class="mentioning-alert-title" color="primary" @click="openChat">{{ mentioning.title }}</strong>
    </v-alert>
</template>

<script>
export default {
    name: "MentioningAlert",
    props: {
        mentioning: {
            required: true
        }
    },
    methods: {
        openChat() {
            this.$store.dispatch('sections/scrollToSection', this.mentioning.sectionId)
            this.$store.dispatch('messages/markMessageMentioningAsRead', this.mentioning.id)
            this.$store.dispatch('messages/setActiveChat', {
                sectionId: this.mentioning.sectionId,
                projectId: this.mentioning.projectId,
                messageId: this.mentioning.messageId,
                title: this.mentioning.title,
                open: true
            })
        },
        close() {
            this.$store.dispatch('messages/markMessageMentioningAsRead', this.mentioning.id)
        }
    }
}
</script>

<style scoped>
    .mentioning-alert-head {
        display: block;
        margin-bottom: 0.3rem;
    }
    .mentioning-alert-title {
        font-weight: normal;
        font-style: italic;
        display: inline-block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        width: inherit;
    }
    .v-alert {
        color: #fff;
        margin-right: 1rem;
        width: calc(100% - 1rem);
    }
    ::v-deep .v-alert__content {
        width: 100%;
    }
    .icon-col{
        min-width: unset;
    }
    .icon{
        color: #fff;
        cursor: pointer;
    }
</style>
