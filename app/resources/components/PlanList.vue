<template>
    <div id="plan-list" class="box">
        <div class="list-header">
            -- 開発計画 --
        </div>
        <hr/>
        <table class="list-body">
            <tbody>
                    <tr
                        v-for="[index, plan] of Object.entries(this.$store.state.plan)"
                        key="islandPlan.id"
                        @click="onClickPlan(index)"
                    >
                        <td><a>{{ parseInt(index)+1 }}</a></td>
                        <td><a>：</a></td>
                        <td><a> <span v-if="plan.data.usePoint">地点 ({{ plan.data.point.x }},{{ plan.data.point.y }}) に</span> {{ plan.data.name }}</a></td>
                    </tr>
            </tbody>
        </table>
    </div>
</template>

<script lang="ts">

export default {
    components: {  },
    data() {
        return {
        }
    },
    setup() {
    },
    methods: {
        onClickPlan(index) {
            this.$store.state.selectedPlanNumber = parseInt(index)+1
        }
    },
    mounted() {
        this.$store.state.plan = JSON.parse(this.islandPlans.plan)
    },
    computed: {},
    props: ['hakoniwa', 'island', 'islandPlans'],
};
</script>

<style lang="scss" scoped>
@import "bulma/bulma.sass";
#plan-list {
    background-color: #e2e8f0;
    margin: 0 10px 16px 10px;
    padding: 10px;
    min-width: 230px;
    max-width: 230px;
    max-height: 480px;
    text-align: left;
    overflow: visible scroll;
}

.list-header {
    text-align: center;
    margin: 2px 0;
    font-size: 16px;
}

.list-body {
    font-size: 14px;
}

hr {
    margin: 8px 0;
}

</style>
