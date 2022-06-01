<template>
    <default>
        <template v-slot:content-header>
            <span>Einstellungen</span>
        </template>
        <template v-slot:content-body>
            <div class="slim-page-container">
                <v-card class="slim-page container-padding">
                    <p class="settings-title">Daily-Digest</p>
                    <v-form lazy-validation>
                        <v-checkbox label="Test" required />
                        <v-radio-group>
                            <v-radio label="Test1" />
                            <v-radio label="Test2" />
                            <v-radio label="Test3" />
                        </v-radio-group>
                    </v-form>
                    <p class="settings-title mt-4">Benachrichtigungen</p>
                    <v-form lazy-validation>
                        <v-checkbox label="Test" required />
                        <v-radio-group>
                            <v-radio label="Test1" />
                            <v-radio label="Test2" />
                            <v-radio label="Test3" />
                        </v-radio-group>
                    </v-form>
                </v-card>
            </div>
        </template>
    </default>
</template>

<script>
import Default from "../layouts/Default"

export default {
    name: "Settings",
    components: {Default},
}
</script>

<style scoped>
.container-padding {
  padding: 1rem;
}

.settings-title {
  font-size: 1.25rem;
}
</style>