<template>
    <div id="island-viewer" class="wrapper">
        <div class="title">{{ island.name }}島へようこそ！</div>
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
import StatusTable from "./StatusTable.vue";
import LogViewer from "./LogViewer.vue";
import IslandViewer from "./IslandViewer.vue";

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
@import "bulma/bulma.sass";

#island-viewer {
    text-align: center;
    margin: 0 auto;
    max-width: 800px;
}

#island {
    position: relative;
    margin: 0 auto;
    max-width: 480px;
    min-width: 496px;
    min-height: 496px;
}

.row {
    display: grid;
    grid-template-columns: repeat(16, 1fr);
}

.cell {
    width: 32px;
    height: 32px;
}

.left-padding {
    width: 16px;
    height: 32px;
    background-image: url("/img/hakoniwa/hakogif/land0.gif");
    background-position: left;
}

.right-padding {
    width: 16px;
    height: 32px;
    background-image: url("/img/hakoniwa/hakogif/land0.gif");
    background-position: right;

    color: white;
    font-size: 10px;
    padding-top: 8px;
}

.hover-window {
    text-align: left;
    padding: 10px;
    margin: 10px;
    position: absolute;
    border: 1px solid;
    background-color: lightyellow;
    min-width: 200px;
    min-height: 50px;
}

</style>
