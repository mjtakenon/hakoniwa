<template>
    <markdown :sources="data"></markdown>
    <div v-if="update_at !== null" class="update-at">{{updateString}}</div>
</template>

<script lang="ts">
import {defineComponent} from 'vue'
import axios from "axios";
import Markdown from "./Markdown.vue";
import {formatDate} from "../Utils";

export default defineComponent({
    components: {
        Markdown,
    },
    data() {
        return {
            data: "",
            update_at: null as Date | null
        }
    },
    computed: {
        updateString() {
            if (this.update_at !== null) {
                return "最終アップデート日時: " + formatDate(this.update_at);
            } else {
                return null;
            }
        }
    },
    mounted() {
        this.getReleases();
    },
    methods: {
        async getReleases() {
            await axios.get("https://api.github.com/repos/mjtakenon/hakoniwa/releases/latest")
                .then(res => {
                    this.data = res.data.body;
                    this.update_at = new Date(res.data.created_at);
                })
                .catch(err => {
                    console.debug(err);
                    this.data = "現在情報はありません";
                })
        }
    }
})
</script>

<style scoped lang="scss">
.update-at {
    @apply my-2 text-right text-on-surface-variant text-xs md:text-sm;
}
</style>
