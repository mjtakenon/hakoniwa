<template>
    <div id="plan-page" class="wrapper">
        <div class="title">{{ $store.state.island.name }}島開発計画</div>
        <div class="subtitle"><a href="/">トップへ戻る</a></div>
        <status-table></status-table>
        <hr/>
        <div class="is-flex is-flex-direction-row">
            <plan-controller></plan-controller>
            <island-editor></island-editor>
            <plan-list></plan-list>
        </div>
        <hr/>
        <log-viewer></log-viewer>
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
        IslandEditor,
        PlanController,
        StatusTable,
        LogViewer,
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
    },
    beforeMount() {
        this.$store.state.hakoniwa = this.hakoniwa
        this.$store.state.island = this.island
        this.$store.state.status = this.island.status
        this.$store.state.terrains = this.island.terrains
        this.$store.state.logs = this.island.logs
        this.$store.state.plans = lodash.cloneDeep(this.island.plans)
        this.$store.state.sentPlans = lodash.cloneDeep(this.island.plans)
        this.$store.state.planCandidate = this.planCandidate
        this.$store.state.targetIslands = this.targetIslands
        this.$store.state.selectedTargetIsland = this.$store.state.island.id
    },
    computed: {},
    props: ['hakoniwa', 'island', 'planCandidate', 'targetIslands'],
};
</script>

<style lang="scss" scoped>

#plan-page {
    text-align: center;
    margin: 0 auto;
    max-width: 1000px;
}

</style>
