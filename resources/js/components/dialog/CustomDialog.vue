<template>
    <v-dialog
        v-model="dialog"
        :max-width="this.narrow ? '900px' : '1060px'"
        persistent
        class="custom-dialog"
    >
        <v-card :loading="loading">
            <v-toolbar class="elevation-0" dark color="primary">
                <v-toolbar-title>
                    <slot name="title"></slot>
                </v-toolbar-title>
                <v-spacer></v-spacer>
                <v-toolbar-items>
                    <v-btn icon dark @click="close">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-toolbar-items>
            </v-toolbar>
            <v-card-text class="mt-2 dialog-content-container">
                <slot name="content"></slot>
            </v-card-text>
            <v-card-actions class="action-buttons">
                <div class="action-buttons-container">
                    <v-btn
                        color="primary"
                        text
                        @click="close"
                        v-if="showCloseButton && !showAbortButton"
                    >
                        Schließen
                    </v-btn>
                    <v-btn
                        color="primary"
                        text
                        @click="close"
                        v-if="showAbortButton"
                    >
                        Abbrechen
                    </v-btn>
                    <slot name="actions">
                    </slot>
                </div>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    name: "CustomDialog",
    props: {
        dialog: {
            type: Boolean,
            default: false
        },
        showCloseButton: {
            type: Boolean,
            default: true
        },
        showAbortButton: {
            type: Boolean,
            default: false
        },
        loading: {
            type: Boolean,
            default: false
        },
        narrow: {
            type: Boolean,
            default: false
        }
    },
    methods: {
        close () {
            this.$emit('close')
        }
    }
}
</script>

<style scoped lang="scss">

>>> .v-dialog {
  max-height: 90vh;
}

.action-buttons {
  display: flex;
  flex-direction: row-reverse;

  .action-buttons-container {
    display: flex;
  }
}

</style>

<style>
.action-buttons-container > .v-btn {
  margin-left: 2px !important;
  margin-right: 2px !important;
}
</style>
