<template>
    <div v-if="isIntro">
        <custom-dialog :dialog="dialog" :show-close-button="false" @close="dialog = false">
            <template v-slot:title>
                Intro Video
            </template>
            <template v-slot:content>
                <video width="450" controls>
                    <source src="/files/IntroVideo.mp4/deliver" type="video/mp4">
                    Your browser does not support HTML video.
                </video>
            </template>
        </custom-dialog>
    </div>
</template>

<script>
import {createNamespacedHelpers} from 'vuex'
import CustomDialog from "../dialog/CustomDialog"
const {mapGetters, mapActions} = createNamespacedHelpers('user')

export default {
    name: "IntroVideo",
    components: {CustomDialog},
    data() {
        return {
            dialog: true,
        }
    },
    computed: {
        ...mapGetters(['getUser']),
        isIntro() {
            if(!this.getUser){
                return false
            }else{
                return !((this.getUser && this.getUser.meta.hasSeenIntroVideo === 1))
            }
        }
    },
    methods: {
        ...mapActions(['markIntroVideoAsSeen'])
    },
    watch: {
        dialog: function() {
            if(!this.dialog) {
                this.markIntroVideoAsSeen()
            }
        }
    }
}
</script>

<style scoped>

</style>
