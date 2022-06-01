<template>
    <default>
        <template v-slot:content-header>
            <span>Daten Export</span>
        </template>
        <template v-slot:content-body>
            <div class="slim-page-container">
                <v-card class="slim-page">
                    <v-card-title></v-card-title>
                    <v-list-item>
                        <v-list-item-title>Der Daten Export wird einmal täglich um 0:00 neu erzeugt.</v-list-item-title>
                    </v-list-item>
                    <v-divider></v-divider>
                    <v-list-item-group v-if="dataExport">
                        <v-list-item>
                            <v-list-item-title>{{
                                'Letzte Generierung des Daten Exports: ' + dataExport.createdAt
                            }}
                            </v-list-item-title>
                        </v-list-item>
                        <v-list-item>
                            <v-list-item-title>{{
                                'Anzahl der Statements des aktuellen Daten Exports: ' + dataExport.statement_count
                            }}
                            </v-list-item-title>
                        </v-list-item>
                        <v-list-item>
                            <v-list-item-title>{{
                                'Anzahl der Downloads des aktuellen Daten Exports: ' + dataExport.downloaded_count
                            }}
                            </v-list-item-title>
                        </v-list-item>
                        <v-list-item>
                            <v-list-item-title>{{ 'Größe des aktuellen Daten Exports: ' + dataExport.filesize }}
                            </v-list-item-title>
                        </v-list-item>
                    </v-list-item-group>
                    <v-list-item v-else>
                        <v-list-item-title>Kein Datenexport vorhanden</v-list-item-title>
                    </v-list-item>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                            href="rest/data/download"
                            color="primary"
                            :disabled="!dataExport"
                            style="text-decoration: none"
                        >Download
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </div>
        </template>
    </default>
</template>

<script>
import axios from "axios"
import DataExport from "../models/dataExport"
import Default from "../layouts/Default"

export default {
    name: "DataExport",
    components: {Default},
    data() {
        return {
            dataExport: null
        }
    },
    methods: {
        getLastDataExport() {
            return new Promise((resolve, reject) => {
                axios.get('rest/data/last').then((response) => {
                    resolve(response.data)
                }, (e) => {
                    console.error(e)
                    reject(e)
                })
            })
        }
    },
    created() {
        this.getLastDataExport().then((exportData) => {
            this.dataExport = DataExport.create(exportData)
        })
    }
}
</script>

<style scoped>
</style>

