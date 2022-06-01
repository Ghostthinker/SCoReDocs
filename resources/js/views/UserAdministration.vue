<template>
    <default>
        <template v-slot:content-header>
            <span>Benutzerverwaltung</span>
        </template>
        <template v-slot:content-body>
            <div class="slim-page-container">
                <v-card class="slim-page">
                    <v-card-text>
                        <v-data-table
                            :headers="headers"
                            :items="users"
                            :sort-by="['name']"
                            :sort-desc="[false, true]"
                            multi-sort
                            class="elevation-1"
                        >
                            <template v-slot:item.roles="{ item }">
                                <v-select
                                    :items="roles"
                                    item-text="name"
                                    item-value="id"
                                    v-model="item.roles[0]"
                                    @change="onSelect(item.roles[0], item.id)"
                                ></v-select>
                            </template>
                        </v-data-table>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="primary" @click="saveRoles">Speichern</v-btn>
                    </v-card-actions>
                </v-card>
            </div>
        </template>
    </default>
</template>

<script>
import axios from "axios"
import Default from "../layouts/Default"

export default {
    name: "UserAdministration",
    components: {Default},
    data() {
        return {
            users: [],
            editedUsers: [],
            roles: [],
            headers: [
                {
                    text: 'Name',
                    align: 'start',
                    value: 'name',
                },
                {text: 'Email', value: 'email'},
                {text: 'Rollen', value: 'roles', sortable: false}
            ]
        }
    },
    methods: {
        getUsers() {
            return new Promise((resolve, reject)  => {
                axios.get('/rest/users').then((response) => {
                    resolve(response.data)
                }, (e) => {
                    console.error(e)
                    reject(e)
                })
            })
        },
        getRoles() {
            return new Promise((resolve, reject) => {
                axios.get('/rest/users/roles').then((response) => {
                    resolve(response.data)
                }, (e) => {
                    console.error(e)
                    reject(e)
                })
            })
        },
        async saveRoles() {
            if (!this.editedUsers.length) {
                this.$store.dispatch('notifications/newNotification', {message: 'Keine Änderungen', type: 'warning'})
                return
            }

            try {
                await this.setRoles()
                this.$store.dispatch('notifications/newNotification', {message: 'Speichern erfolgreich', type: 'success'})
                this.editedUsers = []
            }catch (e) {
                const response = e.response
                if (response.status === 403) {
                    this.$store.dispatch('notifications/newNotification', {
                        message: 'Keine Berechtigung',
                        type: 'error'
                    })
                    return
                } else {
                    this.$store.dispatch('notifications/newNotification', {
                        message: 'Konnte nicht gespeichert werden',
                        type: 'error'
                    })
                    return
                }
            }

        },
        setRoles() {
            return new Promise((resolve, reject)  => {
                axios.patch('/rest/users/roles', this.editedUsers).then((response) => {
                    resolve(response)
                }, (e) => {
                    reject(e)
                })
            })
        },
        onSelect(roleId, userId) {
            this.editedUsers = this.editedUsers.filter(x => x.userId !== userId)
            this.editedUsers.push({
                'roleId': roleId,
                'userId': userId
            })
        }
    },
    created() {
        this.getUsers().then((users) => {
            this.users = users
        })
        this.getRoles().then((roles) => {
            this.roles = roles
        })
    }
}
</script>

<style scoped>
</style>
