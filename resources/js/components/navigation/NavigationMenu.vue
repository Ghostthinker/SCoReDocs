<template>
    <v-list class="navigation-container" v-if="getUser">
        <router-link v-if="getUser.meta.canAccessUserAdministration" class="menu-links" :to="{ name: 'user-administration' }">
            Benutzerverwaltung
        </router-link>
        <router-link v-if="getUser.meta.canAccessAssessmentOverview" class="menu-links" :to="{ name: 'assessment-overview' }">
            Assessmentübersicht
        </router-link>
        <router-link v-if="getUser.meta.canAccessTemplate" class="menu-links" :to="{ name: 'template' }">
            Vorlagen
        </router-link>
        <router-link class="menu-links" :to="{ name: 'archive' }">
            Archiv
        </router-link>
        <router-link v-if="getUser.meta.canAccessDataExport" class="menu-links" :to="{ name: 'data-export' }">
            Daten Export
        </router-link>
        <router-link v-if="getUser.meta.canAccessAssessmentDoc" class="menu-links" :to="{ name: 'project', params: { projectId: getUser.assessmentDocId }}">
            Assessment
        </router-link>
        <a v-if="getUser.meta.canAccessDownloadAgreementDataProcessing" class="menu-links" target="_blank" :href="downloadUrl">
            Erklärungen zur Datenverarbeitung
        </a>
        <router-link class="menu-links" :to="{ name: 'profile' }">
            Profil
        </router-link>
        <!--
        <router-link class="menu-links" :to="{ name: 'settings' }">
            Einstellungen
        </router-link>
        -->

    </v-list>
</template>

<script>
export default {
    name: "NavigationMenu",
    computed: {
        downloadUrl() {
            return window.location.origin + '/download-agreements-data-processing'
        },
        getUser() {
            return this.$store.getters['user/getUser']
        }
    }
}
</script>

<style scoped>
.navigation-container {
    font-size: 1.25em;
    padding-left: 1em;
    display: flex;
    flex-direction: column;
}

.menu-links {
    color: var(--v-primary-base);
}
</style>
