<template>
    <div id="plan-page" class="wrapper">
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
import StatusTable from "../components/StatusTable.vue";
import LogViewer from "../components/LogViewer.vue";
import IslandEditor from "../components/IslandEditor.vue";
import PlanController from "../components/PlanController.vue";
import PlanList from "../components/PlanList.vue";
import lodash from 'lodash';

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
        // console.log(this.islandPlans)

        this.$store.state.plan = lodash.cloneDeep(this.islandPlans)
        this.$store.state.sentPlan = lodash.cloneDeep(this.islandPlans)
        this.$store.state.island = this.island
    },
    computed: {},
    props: ['hakoniwa', 'island', 'islandStatus', 'islandPlans', 'islandTerrain', 'islandLog', 'planList'],
};
</script>

<style lang="scss" scoped>

#plan-page {
    text-align: center;
    margin: 0 auto;
    max-width: 1000px;
}

</style>
