<template>
    <default>
        <template v-slot:content-header>
            <span>Assessmentübersicht</span>
        </template>
        <template v-slot:content-body>
            <div class="slim-page-container">
                <v-card class="slim-page">
                    <v-card-title>
                        Statusübersicht Assessmentdocs
                        <v-spacer></v-spacer>
                        <v-text-field
                            v-model="search"
                            append-icon="mdi-magnify"
                            label="Search"
                            single-line
                            hide-details
                        ></v-text-field>
                        <v-select
                            class="assessment-doc-filter"
                            :items="filterOptions"
                            v-model="filter"
                            label="Filter"
                        ></v-select>
                    </v-card-title>
                    <v-card-text class="custom-table-columns">
                        <v-data-table
                            :loading="loading"
                            :headers="headers"
                            :items="getFilteredContent"
                            :search="search"
                            class="elevation-1"
                        >
                            <template v-slot:item.linkToAssessment="{ item }">
                                <v-btn
                                    class="assessment-doc-link"
                                    text
                                    :href=item.assessmentdoclink
                                >{{item.assessmentdoclink}}</v-btn>
                            </template>
                            <template v-slot:item.assessmentStatus="{ item }">
                                <assessment-overview-status-indicator
                                    :status=parseInt(item.status)
                                    :status-text=item.statusText
                                    class="assessment-doc-status"
                                >
                                </assessment-overview-status-indicator>
                            </template>
                        </v-data-table>
                    </v-card-text>
                </v-card>
            </div>
        </template>
    </default>
</template>

<script>
import axios from "axios"
import AssessmentOverviewStatusIndicator from "../components/assesmentoverview/AssessmentOverviewStatusIndicator"
import Default from "../layouts/Default"

export default {
    name: "AssessmentOverview",
    components: {Default, AssessmentOverviewStatusIndicator},
    data() {
        return {
            overviewEntries: [],
            filteredEntries: [],
            search: '',
            filter: 'Alle',
            filterOptions: [
                'Alle',
                'In Bearbeitung',
                'Eingereicht',
                'In Prüfung',
                'Abgeschlossen'
            ],
            loading: true,
            headers: [
                {text: 'Status', value: 'assessmentStatus'},
                {
                    text: 'Name',
                    align: 'start',
                    value: 'name',
                },
                {text: 'Email', value: 'email'},
                {text: 'Matrikelnummer', value: 'matrNr'},
                {text: 'Link zum Assessmentdoc', value: 'linkToAssessment'}
            ]
        }
    },
    computed: {
        getFilteredContent() {
            switch (this.filter) {
            case 'Alle':
                return this.overviewEntries
            case 'In Bearbeitung':
                return this.overviewEntries.filter(entry => entry.status === 1)
            case 'Eingereicht':
                return this.overviewEntries.filter(entry => entry.status === 2)
            case 'In Prüfung':
                return this.overviewEntries.filter(entry => entry.status === 3)
            case 'Abgeschlossen':
                return this.overviewEntries.filter(entry => entry.status === 4)
            }
        }
    },
    methods: {
        getAssessmentViewData() {
            return new Promise((resolve, reject) => {
                axios.get('rest/assessment-overview-data').then((response) => {
                    resolve(response.data)
                }, (e) => {
                    console.error(e)
                    reject(e)
                })
            })
        }
    },
    created() {
        this.getAssessmentViewData().then((overviewData) => {
            this.overviewEntries = overviewData
        }).then( () => {
            this.loading = false
        })
    }
}
</script>

<style scoped lang="scss">
    .assessment-doc-link {
        text-transform: none;
        padding: 0px !important;
    }
    .assessment-doc-status {
        display: flex;
        justify-content: flex-start;
    }
    .assessment-doc-filter {
        margin-bottom: -22px;
        padding-left: 2em;
        max-width: 225px;
    }
</style>

<style scoped>
    >>>td {
        max-width: 225px !important;
    }
</style>
