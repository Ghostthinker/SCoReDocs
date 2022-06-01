<template>
    <v-footer
        app
        padless
        :absolute="absolute"

        color="white"
        width="100%"
    >
        <v-card
            class="flex"
            flat
            tile
        >
            <v-card-title class="footer-top">
                <div class="score-footer-link">
                    <a :href="'/imprint'"> Impressum </a>
                </div>
                <div class="score-footer-link">
                    <a :href="'/privacy'"> Datenschutzhinweis & Nutzungsbestimmungen </a>
                </div>
            </v-card-title>

            <v-card-text class="py-2 text-center">
                {{ new Date().getFullYear() }} — <strong>SCoRe</strong>
            </v-card-text>
        </v-card>
    </v-footer>
</template>

<script>
export default {
    name: "custom-vue-score-footer",
    props: {
        absolute: {
            required: false,
            default: true
        }
    }
}
</script>

<style scoped>
.score-footer-link {
  padding: 0.5em;
}

.footer-top {
  font-size: 1rem;
  height: 1.25em;
  padding: 0;
}
</style>
