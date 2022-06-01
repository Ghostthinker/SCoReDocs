<template>
    <default>
        <template v-slot:content-header>
            <span>Impressum</span>
        </template>
        <template v-slot:content-body>
            <div class="flex-row imprint-wrapper">
                <div class="slim-page">
                    <v-card class="text-card">
                        <v-card-title>
                            Verantwortlich für den Inhalt:
                        </v-card-title>
                        <v-card-text>
                            Virtuelle Akademie Nachhaltigkeit<br />
                            Universität Bremen<br />
                            Bibliothekstraße 1<br />
                            28359 Bremen<br />
                            <br />
                            Telefon +49 421 218-66788<br />
                            Internet: www.uni-bremen.de<br />
                            <br />
                            Die Universität Bremen ist eine Körperschaft des Öffentlichen Rechts.  Sie wird durch den Rektor Prof. Dr.-Ing. Bernd Scholz-Reiter gesetzlich vertreten. Zuständige Aufsichtsbehörde ist die Senatorin für Bildung und Wissenschaft, Rembertiring 8 – 12, 28195 Bremen.<br />
                            <br />
                            Umsatzsteuer-Identifikationsnummer gemäß § 27 a UStG Umsatzsteuer-Nr.: DE 811 245 070<br />
                        </v-card-text>
                    </v-card>
                    <v-card class="text-card">
                        <v-card-title>
                            Verantwortlich für die technische Bereitstellung:
                        </v-card-title>
                        <v-card-text>
                            Ghostthinker GmbH<br />
                            Hunoldsberg 5<br />
                            86150 Augsburg<br />
                            <br />
                            Amtsgericht Augsburg | HRB 34725<br />
                            USt-IdNr.: DE241472932<br />
                            Internet: www.ghostthinker.de<br />
                            Geschäftsführung: Dr. Frank Vohle und Johannes Metscher<br />
                            <br />
                            edubreak® ist eine beim europäischen Patentamt eingetragene Marke.<br />
                        </v-card-text>
                    </v-card>
                </div>
            </div>
        </template>
    </default>
</template>

<script>
import Default from "../layouts/Default"
export default {
    name: "Imprint",
    components: {Default}
}
</script>

<style scoped lang="scss">
    .imprint-wrapper {
        justify-content: center;
    }

    .text-card {
        padding: 1em;
        margin-top: 1em;
        font-size: 1rem;
    }
</style>
