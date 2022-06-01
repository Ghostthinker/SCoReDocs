<template>
    <div>
        <div>
            Hiermit bestätige ich, dass ich das
            <a
                href="#"
                @click.stop.prevent="privacyInfoDialog = true"
            >Informationsblatt zur Datenverarbeitung</a>
            und die
            <a
                href="#"
                @click.stop.prevent="privacyTermsDialog = true"
            >Datenschutzhinweise</a>
            gelesen habe
        </div>
        <custom-dialog :dialog="privacyTermsDialog" @close="privacyTermsDialog = false">
            <template v-slot:title>
                Datenschutzsbestimmungen
            </template>
            <template v-slot:content>
                <privacy-policy-text></privacy-policy-text>
            </template>
        </custom-dialog>
        <custom-dialog :dialog="privacyInfoDialog" @close="privacyInfoDialog = false">
            <template v-slot:title>
                Informationsblatt zur Datenverarbeitung
            </template>
            <template v-slot:content>
                <privacy-info-text></privacy-info-text>
            </template>
        </custom-dialog>
    </div>
</template>

<script>
import PrivacyPolicyText from "./PrivacyPolicyText"
import PrivacyInfoText from "./PrivacyInfoText"
import CustomDialog from "../dialog/CustomDialog"

export default {
    name: "PrivacyPolicy",
    components: {CustomDialog, PrivacyInfoText, PrivacyPolicyText},
    data() {
        return {
            privacyTermsDialog: false,
            privacyInfoDialog: false,
        }
    },
    methods: {}
}
</script>

<style scoped>

</style>
