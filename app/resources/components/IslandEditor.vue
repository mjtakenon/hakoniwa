<template>
    <div id="editor">
        <island-viewer
            :hakoniwa="hakoniwa"
            :island="island"
            :island-status="islandStatus"
            :island-terrain="islandTerrain"
            :island-log="islandLog"
        ></island-viewer>
    </div>
</template>

<script lang="ts">
import IslandViewer from "./IslandViewer.vue";

export default {
    components: { IslandViewer },
    data() {
        return {
        }
    },
    setup() {
    },
    methods: {
    },
    mounted() {
    },
    computed: {},
    props: ['hakoniwa', 'island', 'islandStatus', 'islandTerrain', 'islandLog'],
};
</script>

<style lang="scss" scoped>
@import "bulma/bulma.sass";

</style>
