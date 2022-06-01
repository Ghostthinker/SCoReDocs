<template>
    <div>
        <v-tooltip bottom>
            <template v-slot:activator="{ on }">
                <v-list-item-icon v-on="on">
                    <component :is="getStatusIcon(status).icon" :class="'color-'+getStatusIcon(status).color"></component>
                </v-list-item-icon>
            </template>
            <span>{{ statusText }}</span>
        </v-tooltip>
    </div>
</template>

<script>
import circle_lock from "../icons/circle_lock"
import circle_arrows from "../icons/circle_arrows"
import circle_check from "../icons/circle_check"
import circle_clockcheck from "../icons/circle_clockcheck"
import circle_doublecheck from "../icons/circle_doublecheck"
import Template from "../../views/Template"

export default {
    name: "AssessmentOverviewStatusIndicator",
    components: {Template},
    props: {
        status: '',
        statusText: String
    },
    methods: {
        getStatusIcon(status) {
            switch (status) {
            case 0:
                return {
                    'icon': circle_lock,
                    'color': 'gray'
                }
            case 1:
                return {
                    'icon': circle_arrows,
                    'color': 'orange'
                }
            case 2:
                return {
                    'icon': circle_check,
                    'color': 'brown'
                }
            case 3:
                return {
                    'icon': circle_clockcheck,
                    'color': 'blue'
                }
            case 4:
                return {
                    'icon': circle_doublecheck,
                    'color': 'green'
                }
            default:
                break
            }
        }
    }
}
</script>

<style scoped lang="scss">
.highlighted {
  background-color: #28242414;
}

.color-gray {
  fill: gray;
}

.color-orange {
  fill: #c77802;
}

.color-brown {
  fill: #00a58d;
}

.color-blue {
  fill: #004da5;
}

.color-green {
  fill: #00a58d;
}
</style>
