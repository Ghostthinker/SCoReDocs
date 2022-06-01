<template>
    <v-list-item  :class="formattedSectionHeadingClass(section)">
        <v-tooltip right open-delay="350">
            <template v-slot:activator="{ on }">
                <v-list-item-title v-on="on">
                    <a :href="'#Section-'+section.id" v-if="section.heading < 2"><b>{{ section.title }}</b></a>
                    <a :href="'#Section-'+section.id" v-else>{{ section.title }}</a>
                </v-list-item-title>
            </template>
            <span>{{ section.title }}</span>
        </v-tooltip>
        <div v-if="totalCount > 0">
            <div class="message-count" :class="[userWasInvolvedInSection ? 'bg-yellow' : 'bg-score']">
                {{ totalCount }}
            </div>
        </div>
    </v-list-item>
</template>

<script>
import {createNamespacedHelpers} from 'vuex'

const {mapGetters} = createNamespacedHelpers('messages')
export default {
    name: "ProjectOutlineElement",
    props: {
        section: {
            required: true
        }
    },
    data() {
        return{

        }
    },
    computed: {
        ...mapGetters(['getMessageCount', 'getUserWasInvolvedInSection']),
        totalCount() {
            return this.messageCount + this.getActivitiesCount
        },
        messageCount() {
            return this.getMessageCount(this.section.id)
        },
        userWasInvolvedInSection() {
            return this.getUserWasInvolvedInSection(this.section.id)
        },
        getActivitiesCount() {
            return this.$store.getters['activities/getActivitiesCount'](this.section.id)
        },
    },
    methods:{
        formattedSectionHeadingClass(section){
            return 'pl-'+(section.heading > 1 ? section.heading*10 : section.heading)
        }
    }
}
</script>

<style lang="scss" scoped>
.pl-20 {
    padding-left: 24px !important;
}

.pl-30 {
    padding-left: 44px !important;
}

.pl-40 {
    padding-left: 64px !important;
}

.pl-50 {
    padding-left: 84px !important;
}
.message-count {
    border-radius: 50%;
    height: 22px;
    width: 22px;
    text-align: center;
    color: white;
}

.bg-yellow {
    background: #ffc107;
}
.bg-score{
    background: var(--v-primary-base);
}
</style>
