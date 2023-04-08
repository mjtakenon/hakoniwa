<template>
    <div id="island-viewer" class="wrapper">
        <div class="title">{{ island.name }}島開発計画</div>
        <div class="subtitle"><a href="/">トップへ戻る</a></div>
        <status-table
            :island-status="islandStatus"
        ></status-table>
        <hr/>
        <div class="is-flex is-flex-direction-row">
            <plan-controller
                :hakoniwa="hakoniwa"
                :island="island"
                :island-status="islandStatus"
                :plan-list="planList"
            ></plan-controller>
            <island-editor
                :hakoniwa="hakoniwa"
                :island="island"
                :island-status="islandStatus"
                :island-terrain="islandTerrain"
                :island-log="islandLog"
            ></island-editor>
            <plan-list
                :hakoniwa="hakoniwa"
                :island="island"
            ></plan-list>
        </div>
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
import IslandEditor from "./IslandEditor.vue";
import PlanController from "./PlanController.vue";
import PlanList from "./PlanList.vue";

export default {
    components: {
        PlanController,
        StatusTable,
        LogViewer,
        IslandEditor,
        PlanList,
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
        this.$store.state.plan = JSON.parse(this.islandPlans.plan)
    },
    computed: {},
    props: ['hakoniwa', 'island', 'islandStatus', 'islandPlans', 'islandTerrain', 'islandLog', 'planList'],
};
</script>

<style lang="scss" scoped>
@import "bulma/bulma.sass";

#island-viewer {
    text-align: center;
    margin: 0 auto;
    max-width: 1000px;
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
