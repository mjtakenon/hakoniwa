<template>
    <div id="editor">
        <island-viewer
            :hakoniwa="hakoniwa"
            :island="island"
            :island-status="islandStatus"
            :island-terrain="islandTerrain"
            :island-log="islandLog"
            :plan-list="planList"
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
    props: ['hakoniwa', 'island', 'islandStatus', 'islandTerrain', 'islandLog', 'islandPlans', 'planList'],
};
</script>

<style lang="scss" scoped>
@import "../../../node_modules/bulma/bulma";

</style>
