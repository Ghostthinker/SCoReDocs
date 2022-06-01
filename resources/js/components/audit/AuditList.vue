<template>
    <div>
        <custom-dialog :dialog="show" show-abort-button @close="close">
            <template v-slot:title>
                Versionsverlauf
            </template>
            <template v-slot:content>
                <v-alert
                    :value="alert"
                    type="warning"
                    transition="scale-transition"
                >
                    Der Inhalt des Abschnitts hat sich geändert. Bitte öffnen sie die Historie erneut um die neuen Versionen einzusehen.
                </v-alert>
                <v-data-table
                    :headers="headers"
                    :items="audits"
                    item-key="id"
                    :items-per-page="10"
                    :loading="loading"
                    loading-text="Lade Versionen... Bitte warten"
                    class="elevation-1 audit-table"
                    :sort-by="['created']"
                    :sort-desc="[true]"
                    show-expand
                    :expanded.sync="expanded"
                    :options.sync="pagination"
                    :server-items-length="totalAudits"
                >
                    <template v-slot:item.selected="{ item }">
                        <v-simple-checkbox @click="selectAudit(item)" v-model="item.selected"></v-simple-checkbox>
                    </template>
                    <template
                        v-slot:item.actions="{ item }"
                    >
                        <v-tooltip bottom :disabled="canRevert">
                            <template v-slot:activator="{ on, attrs }">
                                <div
                                    v-on="on"
                                    v-bind="attrs"
                                    style="cursor: default"
                                >
                                    <v-icon
                                        @click="askForRevert(item)"
                                        :disabled="!canRevert"
                                    >
                                        mdi-backup-restore
                                    </v-icon>
                                </div>
                            </template>
                            <span>{{ disabledRevertMessage }}</span>
                        </v-tooltip>
                    </template>
                    <template v-slot:item.changeLog="{ item }">
                        <td class="audit-log-preview">{{ item.changeLog }}</td>
                    </template>
                    <template v-slot:expanded-item="{ headers, item }">
                        <td class="audit-tag-full" :colspan="headers.length"><b>Änderungsbeschreibung:</b>
                            {{ item.changeLog }}
                        </td>
                    </template>
                </v-data-table>
            </template>
            <template v-slot:actions>
                <v-tooltip bottom>
                    <template v-slot:activator="{ on, attrs }">
                        <div
                            v-on="on"
                            v-bind="attrs"
                        >
                            <v-btn
                                color="primary"
                                @click="compare"
                                :disabled="selected.length !== 2"
                            >
                                Vergleichen
                            </v-btn>
                        </div>
                    </template>
                    <span>Wähle zwei Versionen aus, um diese miteinander zu vergleichen</span>
                </v-tooltip>
            </template>
        </custom-dialog>
        <v-snackbar
            v-model="showErrorAlert"
            multi-line
            timeout="10000"
        >
            {{ errorMessage }}
            <template v-slot:action="{ attrs }">
                <v-btn
                    color="red"
                    text
                    v-bind="attrs"
                    @click="showErrorAlert = false"
                >
                    Schließen
                </v-btn>
            </template>
        </v-snackbar>
        <custom-dialog narrow :dialog="confirmDialog" show-abort-button @close="cancelRevert">
            <template v-slot:title>
                Version zurücksetzen
            </template>
            <template v-slot:content>
                Wollen Sie die Version wirklich zurücksetzen?
                Der derzeitige Inhalt des Editor wird mit dem Inhalt der ausgewählten Version überschrieben.
                Sie haben die Möglichkeit den Text vor dem Speichern noch zu bearbeiten.
            </template>
            <template v-slot:actions>
                <v-tooltip bottom :disabled="canRevert">
                    <template v-slot:activator="{ on, attrs }">
                        <div
                            v-on="on"
                            v-bind="attrs"
                            style="cursor: default"
                        >
                            <v-btn
                                :disabled="!canRevert"
                                color="primary"
                                @click="revert()"
                            >
                                Zurücksetzen
                            </v-btn>
                        </div>
                    </template>
                    <span>{{ disabledRevertMessage }}</span>
                </v-tooltip>
            </template>
        </custom-dialog>
        <custom-dialog :dialog="showCompare" @close="cancelCompare">
            <template v-slot:title>
                Versionen vergleichen
            </template>
            <template v-slot:content>
                <section-diff v-if="showCompare"
                              :audit="compareAudits"
                ></section-diff>
            </template>
        </custom-dialog>
    </div>
</template>

<script>
import axios from "axios"
import SectionDiff from "../section/SectionDiff"
import CustomDialog from "../dialog/CustomDialog"

export default {
    name: "AuditList",
    components: {
        CustomDialog,
        SectionDiff
    },
    data() {
        return {
            loading: true,
            audits: [],
            expanded: [],
            selected: [],
            headers: [
                {
                    text: 'Versionsnummer',
                    align: 'start',
                    sortable: false,
                    value: 'id',
                },
                {
                    text: 'Datum',
                    align: 'start',
                    sortable: false,
                    value: 'created',
                },
                {text: 'Benutzer', value: 'userName', sortable: false,},
                {text: 'Änderungsbeschreibung', value: 'changeLog', sortable: false},
                {text: '', value: 'data-table-expand', align: 'start'},
                {text: 'Versionen vergleichen (max 2)', value: 'selected', sortable: false, align: 'center'},
                {text: 'Zurücksetzen', value: 'actions', sortable: false, align: 'center'},
            ],
            confirmDialog: false,
            selectedRevertItem: null,
            showErrorAlert: false,
            errorMessage: "Es trat ein Fehler beim Anzeigen der Versionen auf!",
            showCompare: false,
            compareAudits: {},
            totalAudits: 0,
            pagination: {},
            alert: false
        }
    },
    props: {
        show: {
            default: false,
            type: Boolean
        },
        section: {
            type: Object,
            required: true
        },
        isLockbyOtherUser: {
            type: Boolean,
            required: true
        }
    },
    computed: {
        disabledRevertMessage() {
            if (this.isLockbyOtherUser) {
                return "Der Abschnitt kann nicht zurückgesetzt werden, da er momentan von einem anderen Anwender gesperrt ist."
            } else if (!this.section.userIsEntitledToChangeContent) {
                return "Fehlende Berechtigung um Abschnitt zu bearbeiten"
            }
            return ""
        },
        canRevert() {
            return !this.isLockbyOtherUser && this.section.userIsEntitledToChangeContent
        }
    },
    watch: {
        pagination: {
            handler() {
                this.loading = true
                this.audits = []
                this.fetchAudits(this.section,
                                 (this.pagination.page - 1) * this.pagination.itemsPerPage,
                                 this.pagination.itemsPerPage
                ).then((audits) => {
                    this.loading = false
                    let createdAudits = []
                    for (const audit of audits) {
                        createdAudits = [...createdAudits, this.createAuditFrom(audit)]
                    }
                    this.audits = createdAudits
                }, (e) => {
                    this.showError(e)
                    this.loading = false
                })
            },
            deep: true
        },
        'section.content': function(newVal, oldVal){
            if(oldVal.replace(/\n$/, "").localeCompare(newVal) !== 0) {
                this.alert = true
            }
        },
        'section.heading': function(){
            this.alert = true
        },
        'section.status': function(){
            this.alert = true
        },
        'section.title': function(){
            this.alert = true
        }
    },
    methods: {
        close() {
            this.$emit('close')
        },
        url() {
            return '/rest/projects/' + this.section.projectId + '/sections/' + this.section.id
        },
        fetchAudits(section, offset = 0, limit = 100) {
            let url = '/rest/projects/' + section.projectId + '/sections/' + section.id + '/audits'
            let params = {
                offset: offset,
                limit: limit
            }
            return new Promise((resolve, reject) => {
                axios.get(url, {params}).then((response) => {
                    const audits = response.data.audits
                    this.totalAudits = response.data.total
                    resolve(audits)
                }, (e) => {
                    console.error(e)
                    reject(e)
                })
            })
        },
        showError(e) {
            this.errorMessage = e
            this.showErrorAlert = true
        },
        askForRevert(item) {
            this.selectedRevertItem = item
            this.confirmDialog = true
        },
        cancelRevert() {
            this.selectedRevertItem = null
            this.confirmDialog = false
        },
        cancelCompare() {
            this.showCompare = false
            this.compareAudits = {}
        },
        revert() {
            this.$emit('revert-to-content', this.selectedRevertItem)
            this.selectedRevertItem = null
            this.confirmDialog = false
            this.close()
        },
        compare() {
            if (this.selected.length !== 2) {
                this.showError('Es müssen zwei Versionen zum Vergleichen ausgewählt werden')
                return
            }
            const first = this.selected.reduce(function (r, a) {
                return r.created > a.created ? a : r
            })
            const second = this.selected.reduce(function (r, a) {
                return r.created > a.created ? r : a
            })
            this.showCompare = true
            this.compareAudits = {first: first, second: second}
            axios.get('/rest/xapi/projects/' + this.section.projectId + '/sections/' + this.section.id + '/comparedversions/' + first.id + '/' + second.id)
        },
        selectAudit(item) {
            if (item.selected === false) {
                const index = _.findIndex(this.selected, {'id': item.id})
                if (index > -1) {
                    this.selected.splice(index, 1)
                }
                return
            }
            if (this.selected.length > 2) {
                console.error('Can\'t select more than two items to compare! Resetting list...')
                for (const audit of this.audits) {
                    audit.selected = false
                    this.selected = []
                }
                return
            }
            if (this.selected.length === 2) {
                this.selected[1].selected = false
                this.selected[1] = this.selected[0]
                this.selected[0] = item
                return
            }
            if (this.selected.length === 1) {
                this.selected.unshift(item)
                return
            }
            this.selected.push(item)
        },
        isSelected(auditId) {
            const index = _.findIndex(this.selected, {'id': auditId})
            return index > -1
        },
        createAuditFrom(audit) {
            return {
                id: audit.id,
                created: audit.created_at,
                newValues: audit.new_values,
                oldValues: audit.old_values,
                state: audit.state,
                changeLog: audit.change_log,
                user: audit.user_id,
                userName: audit.user_name,
                selected: this.isSelected(audit.id)
            }
        }
    }
}
</script>

<style scoped lang="scss">
.audit-log-preview {
    text-overflow: ellipsis;
    max-width: 12em;
    white-space: nowrap;
    overflow: hidden;
}

.audit-tag-full {
    word-break: break-word;
    padding-top: 0.25em;
    padding-bottom: 0.25em;
}
</style>

<style lang="scss">
.v-data-table tbody tr.v-data-table__expanded__content {
    box-shadow: none !important;
}
</style>
