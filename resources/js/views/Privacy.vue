<template>
    <default>
        <template v-slot:content-header>
            <span>Datenschutzhinweis</span>
        </template>
        <template v-slot:content-body>
            <div class="flex-row privacy-wrapper">
                <div class="slim-page">
                    <v-card class="text-card">
                        <v-card-title class="privacy-title">Datenschutzhinweis & Nutzungsbestimmungen</v-card-title>
                        <v-card-text>
                            <privacy-policy-text></privacy-policy-text>
                        </v-card-text>
                        <v-card-text>
                            <terms-of-use-text></terms-of-use-text>
                        </v-card-text>
                    </v-card>
                </div>
            </div>
        </template>
    </default>
</template>

<script>
import PrivacyPolicyText from "../components/privacy/PrivacyPolicyText"
import TermsOfUseText from "../components/privacy/TermsOfUseText"
import Default from "../layouts/Default"

export default {
    name: "Privacy",
    components: {Default, TermsOfUseText, PrivacyPolicyText}
}
</script>

<style scoped lang="scss">
.privacy-wrapper {
    justify-content: center;
    height: unset;
}

.text-card {
    padding: 1em;
    margin-top: 1em;
    font-size: 1rem;
}

.privacy-title {
    color: var(--v-primary-base);
    font-size: 2rem;
    margin-left: -3px;
}
</style>
