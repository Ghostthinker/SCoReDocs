<template>
    <v-main class="section-diff">
        <v-row
            no-gutters
        >
            <v-col :cols="12" md="12">
                <div class="diff-title" v-html="titleDiff"></div>
                <v-divider class="divider"></v-divider>
                <div class="diff-description" v-html="descriptionDiff"></div>
            </v-col>
        </v-row>
    </v-main>
</template>

<script>
import diff from "node-htmldiff"

export default {
    name: "SectionDiff",
    components: {},
    data() {
        return {
        }
    },
    props: {
        audit: Object,
    },
    computed: {
        titleDiff() {
            let oldTitle = this.audit.first.state.title
            let newTitle = this.audit.second.state.title

            return diff(oldTitle, newTitle, 'score-diff')
        },
        descriptionDiff() {
            let oldDescription = this.audit.first.state.content
            let newDescription = this.audit.second.state.content

            return diff(oldDescription, newDescription, 'score-diff')
        }
    },
    methods: {}
}
</script>
<style lang="scss" scoped>
    .section-diff{
        margin-top: 50px;
    }
    .diff-title{
        font-size: 18px;
    }
    .divider{
        border-color: #00000066!important;
    }
</style>


<style lang="scss">
.section-diff span{
    line-height: normal;
}

ins.score-diff > img{
    background-color: #88ee97;
    padding: 3px;
}

del.score-diff > img{
    background-color: #de8482;
    padding: 3px;
}

ins.score-diff {
    background-color: #88ee97;
}

del.score-diff {
    background-color: #de8482;
}

fieldset {
    label {
        font-size: 1.4em;
        color: var(--v-primary-base);
    }
}
.section-diff {
  img {
    max-width: 100%;
    height: auto;
  }
  table {
    max-width: 960px;
  }
}
</style>
