<template>
    <v-hover v-slot="{ hover }">
        <v-card :disabled="activity.isSectionDeleted" @click="navigateTo" class="activity-card"
                :class="[activity.type === 'atAll' ? 'atall-hightlight' : '', activity.read ? 'status-read' : '']">
            <v-expand-x-transition @afterEnter="afterHover">
                <div
                    v-if="hover && !activity.read"
                    class="transition-fast-in-fast-out grey v-card--reveal"
                    style="height: 100%;"
                >
                </div>
            </v-expand-x-transition>
            <v-card-subtitle class="title project-title activity-title" v-if="!isInProject">Projekt: {{ activity.projectTitle }}
            </v-card-subtitle>
            <v-card-subtitle v-if="activity.isSectionDeleted && activity.sectionTitle" class="title activity-title">Gelöschter
                Abschnitt:
                {{ activity.sectionTitle }}
            </v-card-subtitle>
            <v-card-subtitle v-if="!activity.isSectionDeleted && activity.sectionTitle" class="title activity-title">Abschnitt:
                {{ activity.sectionTitle }}
            </v-card-subtitle>
            <v-card-text class="activity-text">
                {{ activity.message }}
            </v-card-text>
        </v-card>
    </v-hover>
</template>

<script>
import {createNamespacedHelpers} from 'vuex'
import axios from "axios"

const {mapActions} = createNamespacedHelpers('activities')
export default {
    name: "Activity",
    data() {
        return {}
    },
    props: {
        activity: {
            default: null,
            type: Object
        },
        isInProject: {
            type: Boolean,
            default: false
        }
    },
    computed: {},
    methods: {
        ...mapActions(['markAsRead', 'updateActivitiesCount']),
        navigateTo() {
            if (!this.isInProject) {
                if (this.activity.section_id !== null) {
                    this.$router.push('/project/' + this.activity.projectId + '/#Section-' + this.activity.sectionId)
                } else {
                    this.$router.push('/project/' + this.activity.projectId)
                }
            } else {
                let ref = this.$route.hash.substring(1)
                if (ref !== 'Section-' + this.activity.sectionId) {
                    this.$router.replace({hash: '#Section-' + this.activity.sectionId})
                }
                this.scrollToHash()
            }
            this.sendXapiInsert(this.activity)
        },
        scrollMeTo(element) {
            element.scrollIntoView()
        },
        scrollToHash() {
            let ref = this.$route.hash.substring(1)
            let element = null
            let self = this
            element = document.getElementById(ref)
            if (element == null) {
                element = document.querySelector('[ref="' + ref + '"]')
            }
            if (element != null) {
                self.scrollMeTo(element)
            }
        },
        async sendXapiInsert(data) {
            await axios.get('/rest/xapi/activities/' + data.id + '/clicked')
        },
        afterHover() {
            setTimeout(() => {
                this.markAsRead(this.activity.id)
                this.updateActivitiesCount({
                    sectionId: this.activity.sectionId,
                    activityCountDiff: -1
                })
            }, 100)
        }
    },
}
</script>

<style lang="scss" scoped>
.activity-card {
    min-width: 150px;
    margin: 0.5rem;
    padding: 0.25rem;
}

.title {
    padding-bottom: 0.25rem;
    font-size: 1rem !important;
    line-height: 1rem;
}

.project-title {
    font-weight: bold;
}

.activity-text {
    padding-top: 0.5rem;
    max-height: 4.5rem;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    margin-bottom: 0.5rem;
}

.activity-title {
  word-break: break-word;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
  display: -webkit-box;
  padding-bottom: 0;
}

.atall-hightlight {
    background-color: #FF8D00;
}

.status-read {
    opacity: .6;
}

.v-card--reveal {
    align-items: center;
    bottom: 0;
    justify-content: center;
    opacity: .5;
    position: absolute;
    width: 100%;
}
</style>
