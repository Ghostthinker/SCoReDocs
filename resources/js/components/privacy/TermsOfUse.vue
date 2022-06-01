<template>
    <div>
        <div>
            Ich stimme den
            <a
                href="#"
                @click.stop.prevent="dialog = true"
            >Nutzungsbestimmungen</a>
            zu
        </div>
        <custom-dialog :dialog="dialog" @close="dialog = false">
            <template v-slot:title>
                Nutzungsbestimmungen
            </template>
            <template v-slot:content>
                <terms-of-use-text></terms-of-use-text>
            </template>
        </custom-dialog>
    </div>
</template>

<script>
import TermsOfUseText from "./TermsOfUseText"
import CustomDialog from "../dialog/CustomDialog"
export default {
    name: "TermsOfUse",
    components: {CustomDialog, TermsOfUseText},
    data() {
        return {
            dialog: false,
        }
    },
    methods: {
    }
}
</script>

<style scoped>

</style>
