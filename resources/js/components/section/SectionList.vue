<template>
    <div>
        <custom-dialog :dialog="show" @close="close">
            <template v-slot:title>
                Gelöschte Abschnitte
            </template>
            <template v-slot:content>
                <v-data-table
                    :headers="headers"
                    :items="sections"
                    item-key="id"
                    :items-per-page="10"
                    :loading="loading"
                    loading-text="Lade Abschnitte... Bitte warten"
                    class="elevation-1 audit-table"
                    :sort-by="['created']"
                    :sort-desc="[true]"
                    single-expand
                    show-expand
                    :expanded.sync="expanded"
                    :options.sync="pagination"
                    :server-items-length="totalSections"
                    @item-expanded="onClickedExpand"
                >
                    <template
                        v-slot:item.actions="{ item }"
                    >
                        <v-icon
                            @click="askForRevert(item)"
                        >
                            mdi-backup-restore
                        </v-icon>
                    </template>
                    <template v-slot:item.change_log="{ item }">
                        <td class="audit-log-preview">{{ item.change_log }}</td>
                    </template>
                    <template v-slot:expanded-item="{ headers, item }">
                        <td class="audit-tag-full" :colspan="headers.length"><div v-html="previewContent"></div></td>
                    </template>
                </v-data-table>
            </template>
        </custom-dialog>
        <custom-dialog :dialog="confirmDialog" @close="cancelRevert">
            <template v-slot:title>
                Abschnitt wiederherstellen
            </template>
            <template v-slot:content>
                Wollen Sie den Abschnitt wirklich wiederherstellen?
            </template>
            <template v-slot:actions>
                <v-btn
                    color="primary"
                    @click="revert()"
                >
                    Wiederherstellen
                </v-btn>
            </template>
        </custom-dialog>
    </div>
</template>

<script>
import CustomDialog from "../dialog/CustomDialog"
export default {
    name: "SectionList.vue",
    components: {CustomDialog},
    data() {
        return {
            loading: true,
            sections: [],
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
                    value: 'deleted_at',
                },
                { text: 'Benutzer', value: 'name', sortable: false, },
                { text: 'Titel', value: 'title', sortable: false, },
                { text: 'Löschbeschreibung', value: 'change_log', sortable: false },
                { text: 'Inhalt', value: 'data-table-expand', align: 'start' },
                { text: 'Wiederherstellen', value: 'actions', sortable: false, align: 'center' },
            ],
            confirmDialog: false,
            selectedRevertItem: null,
            showErrorAlert: false,
            errorMessage: "Es trat ein Fehler beim Anzeigen der Abschnitte auf!",
            totalSections: 0,
            pagination: {},
            previewContent: 'lade Inhalt...'
        }
    },
    props: {
        show: {
            default: false,
            type: Boolean
        },
        projectId: {
            required: true
        }
    },
    watch: {
        pagination: {
            handler () {
                this.loading = true
                this.sections = []
                this.fetchSections(
                    (this.pagination.page - 1) * this.pagination.itemsPerPage,
                    this.pagination.itemsPerPage
                ).then((sections) => {
                    this.loading = false
                    let createdSections = []
                    for (const section of sections) {
                        createdSections = [...createdSections, section]
                    }
                    this.sections = createdSections
                }, (e) => {
                    this.showError(e)
                    this.loading = false
                })
            },
            deep: true
        }
    },
    methods: {
        close() {
            this.$emit('close')
        },
        fetchSections(offset = 0, limit = 100) {
            let url = '/rest/projects/' + this.projectId + '/sections/deleted/all'
            let params = {
                offset: offset,
                limit: limit
            }
            return new Promise((resolve, reject) => {
                axios.get(url, { params }).then((response) => {
                    const sections = response.data.sections
                    this.totalSections = response.data.total
                    resolve(sections)
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
        revert() {
            this.$emit('revert-section', this.selectedRevertItem.id)
            this.selectedRevertItem = null
            this.confirmDialog = false
            this.close()
        },
        onClickedExpand(row) {
            if(row.value){
                this.previewContent = 'lade Inhalt...'
                const data = {
                    projectId: this.projectId,
                    sectionId: row.item.id
                }
                this.$store.dispatch('sections/getTrashedSection', data).then((response)=>{
                    this.previewContent = response.data.content
                })
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

<style scoped>

</style>
