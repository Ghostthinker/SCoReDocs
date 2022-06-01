<template>
    <custom-dialog :dialog="show" :show-close-button="false" v-on:close="close">
        <template v-slot:title>
            Abschnitt "{{ sectionTitle }}" wird automatisch freigegeben
        </template>
        <template v-slot:content>
            <v-card-text>
                Sie haben den Textblock seit mehr als 8 Minuten gesperrt, jedoch nicht bearbeitet.
                Der Textblock wird nach 2 Minuten wieder für andere freigegeben. Sie können durch einen Klick
                auf "Weiterarbeiten" das Timeout abbrechen.
            </v-card-text>
            <v-progress-linear
                height="15"
                striped
                :value="timeoutTimePercentage"
                color="deep-orange"
            >
                <div>{{ timeoutTimeText }}</div>
            </v-progress-linear>
        </template>
        <template v-slot:actions>
            <v-btn
                color="primary"
                @click="close"
            >
                Weiterarbeiten
            </v-btn>
        </template>
    </custom-dialog>
</template>

<script>

import CustomDialog from "../dialog/CustomDialog"

export default {
    name: "SectionTimeout",
    components: {CustomDialog},
    props: {
        sectionId: {
            required: true
        },
        projectId: {
            required: true
        },
        sectionTitle: {
            required: true
        },
        value: Boolean
    },
    data() {
        return {
            unlockTimeout: null,
            timeoutTimeRemaining: 120,
            timeoutInterval: null,
        }
    },
    computed: {
        timeoutTimePercentage() {
            return this.timeoutTimeRemaining / 1.2
        },
        timeoutTimeText() {
            const minutesLeft = Math.floor(this.timeoutTimeRemaining / 60)
            const secondsLeft = this.timeoutTimeRemaining - minutesLeft * 60
            return minutesLeft + ' min ' + secondsLeft + ' s'
        },
        show: {
            get() {
                return this.value
            },
            set(value) {
                this.$emit('input', value)
            }
        }
    },
    methods: {
        close() {
            this.clearUnlockTimeout()
            this.show = false
            this.$emit('close')
        },
        startCountdown() {
            setInterval(() => {
                if (this.timeoutTimeRemaining > 0) {
                    this.timeoutTimeRemaining -= 1
                }
            }, 1000)
        },
        startUnlockTimeout() {
            console.log('Started unlock timeout.')
            this.unlockTimeout = setTimeout(() => {
                console.log('Unlock timeout exceeded - starting unlock.')
                this.unlockSection()
            }, 120000)
        },
        clearUnlockTimeout() {
            if (this.unlockTimeout) {
                console.log('Cleared unlock timeout.')
                clearTimeout(this.unlockTimeout)
                this.unlockTimeout = null
            }
        },
        unlockSection() {
            this.show = false
            this.$emit('reset')
        }
    },
    created() {
        this.startCountdown()
        this.startUnlockTimeout()
    }
}
</script>

<style scoped>

</style>
