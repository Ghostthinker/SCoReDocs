import Vue from 'vue'
import de from "vuetify/lib/locale/de";
import Vuetify from "vuetify";
import light from "./theme";

Vue.use(Vuetify)

const opts = {
    lang: {
        locales: {de},
        current: 'de',
    },
    theme: {
        dark: false,
        themes: { light },
        options: {
            customProperties: true
        }
    }
}

export default new Vuetify(opts)
