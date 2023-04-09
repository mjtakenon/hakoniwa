<template>
    <div id="sightseeing-page" class="wrapper">
        <div class="title">{{ island.name }}島へようこそ！</div>
        <div class="subtitle"><a href="/app/public">トップへ戻る</a></div>
        <status-table
            :island-status="islandStatus"
        ></status-table>
        <hr/>
        <island-viewer
            :hakoniwa="hakoniwa"
            :island="island"
            :island-status="islandStatus"
            :island-terrain="islandTerrain"
            :island-log="islandLog"
        ></island-viewer>
        <hr/>
        <log-viewer
            :island="island"
            :island-log="islandLog"
        ></log-viewer>
    </div>
</template>

<script lang="ts">
import StatusTable from "../components/StatusTable.vue";
import LogViewer from "../components/LogViewer.vue";
import IslandViewer from "../components/IslandViewer.vue";

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
    methods: {
        getIslandTerrain(x, y) {
            return this.islandTerrain.filter(function(item, idx){
                if (item.data.point.x === x && item.data.point.y === y) return true;
            }).pop();
        },
        onMouseOverCell(x, y) {
            this.showHoverWindow = true;
            this.hoverCell.x = x;
            this.hoverCell.y = y;

            // 左半分
            if (this.hoverCell.x < this.hakoniwa.width / 2) {
                this.hoverWindowLeft = 250;
            } else {
                this.hoverWindowLeft = 0;
            }
        },
        onMouseLeaveCell(x, y) {
            this.showHoverWindow = false;
        }
    },
    mounted() {
        // console.log(this.$props)
    },
    computed: {
        // showHoverWindow() { return true; }
    },
    props: ['hakoniwa', 'island', 'islandStatus', 'islandTerrain', 'islandLog'],
};
</script>

<style lang="scss" scoped>
@import "../../../node_modules/bulma/bulma";

#sightseeing-page {
    text-align: center;
    margin: 0 auto;
    max-width: 800px;
}

</style>
