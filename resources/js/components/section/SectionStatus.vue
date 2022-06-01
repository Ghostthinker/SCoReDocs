<template>
    <div>
        <v-tooltip top>
            <template v-slot:activator="{ on }">
                <span v-on="on" class="icon-container" @click="getAllSectionStatus">
                    <component :is="getStatusIcon(section).icon" :class="'color-'+getStatusIcon(section).color"></component>
                </span>
            </template>
            <span>{{ section.statusText }}</span>
        </v-tooltip>
        <v-card v-if="showStatusDropDown && inEdit && canEditStatus" v-click-outside="onClickOutside" class="card mx-auto"
                max-width="500">
            <v-list class="list">
                <v-list-item-group>
                    <v-list-item v-for="status in getSectionStatus" :key="status.index"
                                 :disabled="!status.allowed"
                                 @click="changeStatus(status)"
                                 :class="(section.status === status.status) ? 'highlighted' : ''"
                    >
                        <v-list-item-icon>
                            <component :is="getStatusIcon(status).icon" :class="'color-'+getStatusIcon(status).color"></component>
                        </v-list-item-icon>
                        <v-list-item-content>
                            {{ status.statusText }}
                        </v-list-item-content>
                    </v-list-item>
                </v-list-item-group>
            </v-list>
        </v-card>
    </div>
</template>

<script>
import { createNamespacedHelpers } from 'vuex'
const { mapGetters, mapActions } = createNamespacedHelpers('sections')

import circle_arrows from "../icons/circle_arrows"
import circle_check from "../icons/circle_check"
import circle_clockcheck from "../icons/circle_clockcheck"
import circle_doublecheck from "../icons/circle_doublecheck"
import circle_lock from "../icons/circle_lock"

export default {
    name: "SectionStatus",
    components: {
        circle_arrows,
        circle_check,
        circle_clockcheck,
        circle_doublecheck,
        circle_lock
    },
    props: {
        section: {
            type: Object,
            required: true
        },
        inEdit: {
            type: Boolean,
            required: true
        },
        canEditStatus: {
            type: Boolean,
            required: true
        }
    },
    data(){
        return {
            showStatusDropDown: false
        }
    },
    computed: {
        ...mapGetters(['getSectionStatus']),
    },
    methods: {
        ...mapActions(['fetchSectionStatus']),
        getAllSectionStatus(){
            console.log('click on section status')
            if(this.inEdit) {
                this.fetchSectionStatus(this.section).then(() => {
                    this.showStatusDropDown = true
                })
            }

        },
        onClickOutside(){
            this.showStatusDropDown = false
        },
        getStatusIcon(section){
            switch(section.status){
            case 0:
                return {
                    'icon': circle_lock,
                    'color': 'gray'
                }
            case 1:
                return {
                    'icon': circle_arrows,
                    'color': 'orange'
                }
            case 2:
                return {
                    'icon': circle_check,
                    'color': 'brown'
                }
            case 3:
                return {
                    'icon': circle_clockcheck,
                    'color': 'blue'
                }
            case 4:
                return {
                    'icon': circle_doublecheck,
                    'color': 'green'
                }
            default:
                break
            }
        },
        changeStatus(status){
            this.section.status = status.status
            this.section.statusText = status.statusText
            this.showStatusDropDown = false
        }
    }
}
</script>

<style scoped lang="scss">
    .highlighted{
        background-color: #28242414;
    }
    .color-gray{
        fill: gray;
    }
    .color-orange{
        fill: #c77802;
    }
    .color-brown{
        fill: #00a58d;
    }
    .color-blue{
        fill: #004da5;
    }
    .color-green{
        fill: #00a58d;
    }
    .list{
        width: 250px;
    }
    .card{
        position: absolute;
        z-index: 10000;
        right: 0px;
    }
</style>
