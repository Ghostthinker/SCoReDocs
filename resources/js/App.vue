<template>
    <v-app>
        <v-main>
            <router-view></router-view>
        </v-main>
    </v-app>
</template>

<script>
export default {}
</script>

<style lang="scss">
// Override vuetify
.theme--light.v-application {
    background: unset;
}

.ckeditor-html {
  margin-top: 0.5rem;
  padding-top: 0.5rem;

  p {
    width: fit-content;
  }

  table {
    max-width: 998px;
  }

  img {
    max-width: 100%;
    height: auto;
  }
}

.horizontally-centered-row {
    display: flex;
    flex-direction: row;
    align-items: center;
}
@media (min-width: 1100px) {
    .slim-page {
        width: 1016px !important;
    }

    .slim-page-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%
    }
}
</style>
