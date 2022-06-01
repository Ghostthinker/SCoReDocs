<template>
    <v-row
        no-gutters
    >
        <v-col md="8" sm="6" cols="6">
            <v-tooltip top open-delay="600" :disabled="inEdit && canEditTitle">
                <template v-slot:activator="{ on }">
                    <div v-on="on">
                        <v-text-field
                            class="section-title"
                            single-line
                            v-model="section.title"
                            color="primary"
                            :class="getHeadingClass()"
                            :disabled="!(inEdit && canEditTitle)"
                        ></v-text-field>
                    </div>
                </template>
                <span>{{ section.title }}</span>
            </v-tooltip>
        </v-col>
        <v-spacer></v-spacer>
        <v-col md="3" sm="4" cols="5">
            <v-select
                v-if="inEdit"
                v-model="selectedHeading"
                :items="headings"
                :disabled="!canEditHeadingType"
                menu-props="auto"
                hide-details
                single-line
                v-on:click="onSelectClick"
            ></v-select>
        </v-col>
        <v-spacer></v-spacer>
        <v-col class="icon-toolbar">
            <section-status :section="section" :inEdit="inEdit" :canEditStatus="canEditStatus"></section-status>
        </v-col>
        <v-spacer></v-spacer>
    </v-row>
</template>

<script>
import SectionStatus from './SectionStatus'

export default {
    name: "SectionHeader",
    components: {
        SectionStatus
    },
    props: {
        inEdit: {
            type: Boolean,
            default: false
        },
        canEditTitle: {
            type: Boolean,
            default: true
        },
        canEditHeadingType: {
            type: Boolean,
            default: true
        },
        canEditStatus: {
            type: Boolean,
            default: true
        },
        section: Object
    },
    data() {
        return {
            headings: [
                {value: 1, text: 'Überschrift 1', class: 'h2'},
                {value: 2, text: 'Überschrift 2', class: 'h3'},
                {value: 3, text: 'Überschrift 3', class: 'h4'},
                {value: 4, text: 'Überschrift 4', class: 'h5'},
                {value: 5, text: 'Überschrift 5', class: 'h6'},
            ]
        }
    },
    computed: {
        selectedHeading: {
            get: function () {
                return this.section.heading
            },
            set: function (heading) {
                this.section.heading = heading
            }
        }
    },
    methods: {
        getHeadingClass() {
            const index = _.findIndex(this.headings, {value: this.selectedHeading})
            if (index > -1) {
                return this.headings[index].class
            }
        },
        onSelectClick() {
            if (typeof this.$parent.$parent.$refs.ckeditor !== "undefined") {
                this.$parent.$parent.$refs.ckeditor.instance.focusManager.blur()
            }
        }
    }
}
</script>

<style scoped>
.icon-toolbar {
  padding-top: 29px!important;
}
</style>
<style lang="scss">
.v-input input {
  min-height: 3rem;
}
</style>
