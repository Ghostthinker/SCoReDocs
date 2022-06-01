<template>
    <default>
        <template v-slot:content-header>
            <span>Profil</span>
        </template>
        <template v-slot:content-body>
            <div class="slim-page-container">
                <div class="slim-page">
                    <v-form
                        ref="form"
                        v-model="valid"
                        lazy-validation
                    >
                        <v-card>
                            <v-card-text>
                                <v-row no-gutters class="mb-4">
                                    <v-col cols="12" md="3" sm="12">
                                        <div class="text-center">
                                            <v-avatar size="150" class="mr-4 mb-4" color="primary">
                                                <img :src="avatarUrl" v-if="avatarUrl" alt="profile-img" class="profile-img">
                                                <v-icon dark v-else size="64">mdi-account-circle</v-icon>
                                            </v-avatar>
                                            <v-file-input
                                                v-model="file"
                                                ref="image"
                                                :rules="rules"
                                                @change="onFileChange"
                                                accept="image/png, image/jpeg, image/bmp"
                                                placeholder="Wähle ein Profilbild"
                                                prepend-icon="mdi-camera"
                                                label="Profilbild"
                                                color="Primary"

                                            ></v-file-input>
                                        </div>
                                    </v-col>
                                    <v-col cols="12" md="9" sm="12" class="mt-sm-8">
                                        <div style="width: 100%">
                                            <v-text-field
                                                v-model="getProfile.name"
                                                label="Name"
                                                required
                                                :readonly="true"
                                            ></v-text-field>
                                        </div>
                                    </v-col>
                                </v-row>
                                <v-text-field
                                    v-model="getProfile.university"
                                    label="Hochschule"
                                ></v-text-field>
                                <v-text-field
                                    v-model="getProfile.course"
                                    label="Studiengang"></v-text-field>
                                <v-text-field
                                    type="number"
                                    v-model="getProfile.matriculationNumber"
                                    label="Matrikelnummer"
                                    class="input-number"></v-text-field>
                                <v-textarea
                                    v-model="getProfile.knowledge"
                                    name=""
                                    label="Kenntnisse/Expertise"
                                    value=""
                                ></v-textarea>
                                <v-textarea
                                    v-model="getProfile.personalResources"
                                    name=""
                                    label="Persönliche Ressourcen"
                                    value=""
                                ></v-textarea>
                                <v-textarea
                                    v-model="getProfile.aboutMe"
                                    name=""
                                    label="Über mich"
                                    value=""
                                ></v-textarea>
                            </v-card-text>
                            <v-card-actions>
                                <v-col class="text-right">
                                    <v-btn color="primary" @click="save" right>
                                        Änderungen speichern
                                    </v-btn>
                                </v-col>
                            </v-card-actions>
                        </v-card>
                    </v-form>
                </div>
            </div>
        </template>
    </default>
</template>

<script>
import { createNamespacedHelpers } from 'vuex'
import Default from "../layouts/Default"
const { mapGetters, mapActions } = createNamespacedHelpers('user')

export default {
    name: "Profile",
    components: {Default},
    data(){
        return{
            rules: [
                value => !value || value.size < 4000000 || 'Avatar Größe muss kleiner als 4 MB sein!',
            ],
            valid: true,
            avatarTemp: {
                url: '',
                data: false
            },
            file: []
        }
    },
    computed:{
        ...mapGetters(['getProfile']),
        avatarUrl(){
            if(this.getProfile &&  this.getProfile.avatar && this.avatarTemp && !this.avatarTemp.url){
                return this.getProfile.avatar
            } else if(this.getProfile && this.avatarTemp && this.avatarTemp.url){
                return this.avatarTemp.url
            } else{
                return false
            }
        }
    },
    methods:{
        ...mapActions(['fetchProfile', 'updateProfile']),
        save(){
            this.validate()
            // this.getProfile.avatarFile =
            let formData = new FormData()
            formData.append('_method', 'PATCH')
            formData.append('aboutMe', (this.getProfile.aboutMe || ''))
            formData.append('course', (this.getProfile.course || ''))
            formData.append('knowledge', (this.getProfile.knowledge || ''))
            formData.append('matriculationNumber', (this.getProfile.matriculationNumber || ''))
            formData.append('personalResources', (this.getProfile.personalResources || ''))
            formData.append('university', (this.getProfile.university || ''))


            if (this.avatarTemp.data) {
                formData.append('avatarFile', this.avatarTemp.data)
            }

            this.updateProfile(formData).then(() => {
                this.$store.dispatch('notifications/newNotification', {
                    message: 'Speichern erfolgreich',
                    type: 'success'
                })

            }).catch((e) => {
                const response = e.response
                if (response.status === 422) {
                    Object.entries(response.data.errors).map(el =>
                        this.$store.dispatch('notifications/newNotification', {
                            message: el[1][0],
                            type: 'error'
                        }))
                } else {
                    console.error(response.status)
                    this.$store.dispatch('notifications/newNotification', {
                        message: 'Speichern war nicht erfolgreich',
                        type: 'error'
                    })
                }
            })

            this.file = null

        },
        validate(){
            this.$refs.form.validate()
        },
        onFileChange(file){
            if(file){
                this.avatarTemp = {url: URL.createObjectURL(file), data: file}
            }else{
                this.avatarTemp.url = false
                this.avatarTemp.data = false
            }

        }
    },
    created(){
        this.fetchProfile()
    }
}
</script>

<style scoped>
.profile-img {
    object-fit: contain;
    background-color: white;
}
</style>

<style>
    /* Chrome, Safari, Edge, Opera */
    .input-number input::-webkit-outer-spin-button,
    .input-number input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* Firefox */
    .input-number input[type=number] {
        -moz-appearance: textfield;
    }
</style>
