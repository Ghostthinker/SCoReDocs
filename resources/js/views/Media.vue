<template>
    <div>

        <router-view>

        </router-view>
    </div>
</template>

<script>
import MediaDashboard from "../components/media/MediaDashboard"
import MediaPlayer from "../components/media/MediaPlayer"

export default {
    name: "Media",
    routes: [
        { path: "/media", component: MediaDashboard },
        { path: "/media/:id", component: MediaPlayer,  props: true },
    ]

}
</script>

<style scoped>

</style>
