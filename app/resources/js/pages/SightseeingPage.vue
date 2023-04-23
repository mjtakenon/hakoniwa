<template>
    <div id="sightseeing-page" class="wrapper">
        <div class="title">{{ $store.state.island.name }}島へようこそ！</div>
        <div class="subtitle"><a href="/">トップへ戻る</a></div>
        <status-table></status-table>
        <hr/>
        <island-viewer></island-viewer>
        <hr/>
        <log-viewer></log-viewer>
    </div>
</template>

<script lang="ts">
import StatusTable from "../components/StatusTable.vue";
import LogViewer from "../components/LogViewer.vue";
import IslandViewer from "../components/IslandViewer.vue";
import lodash from "lodash";

export default {
    components: {
        IslandViewer,
        StatusTable,
        LogViewer,
    },
    data() {
        return {
            showHoverWindow: false,
            hoverCell: {
                "x": 0,
                "y": 0,
            },
            hoverWindowTop: 170,
            hoverWindowLeft: 0,
        }
    },
    setup() {
    },
    methods: {},
    beforeMount() {
        this.$store.state.hakoniwa = this.hakoniwa
        this.$store.state.island = this.island
        this.$store.state.status = this.island.status
        this.$store.state.terrains = this.island.terrains
        this.$store.state.logs = this.island.logs
    },
    computed: {
        // showHoverWindow() { return true; }
    },
    props: ['hakoniwa', 'island'],
};
</script>

<style lang="scss" scoped>

#sightseeing-page {
    text-align: center;
    margin: 0 auto;
    max-width: 800px;
}

</style>
