<template>
    <div class="link-container">
        <router-link :to="{ name: 'imprint' }">
            Impressum
        </router-link>
        <router-link :to="{ name: 'privacy' }">
            Datenschutzhinweis & Nutzungsbestimmungen
        </router-link>
        <span class="py-2 text-center">
            {{ new Date().getFullYear() }} — <strong>SCoRe</strong>
        </span>
    </div>
</template>

<script>
export default {
    name: "DefaultScoreFooter"
}
</script>

<style scoped>
.link-container {
    display: flex;
    flex-direction: column;
}
</style>
