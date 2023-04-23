<template>
    <div id="plan-list" class="box">
        <div class="list-header">
            <span v-if="isPlanSent" class="success-color"> -- 計画送信済み -- </span>
            <span v-else class="danger-color"> -- 計画未送信 -- </span>
        </div>
        <hr/>
        <table class="list-body">
            <tbody>
                <tr
                    v-for="[index, plan] of Object.entries($store.state.plans)"
                    :key="plan"
                    @click="onClickPlan(index)"
                    :style="[ parseInt(index)+1 === $store.state.selectedPlanNumber ? { textDecoration: 'underline' } : '']"
                >
                    <td><a>{{ parseInt(index)+1 }}</a></td>
                    <td><a>：</a></td>
                    <td><a>
                        <span v-if="plan.data.usePoint">地点 ({{ plan.data.point.x }},{{ plan.data.point.y }}) に</span>
                        <span>{{ plan.data.name }}</span>
                        <span v-if="plan.data.amount >= 2"> ({{ plan.data.amount }}回実施)</span></a></td>
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
    mounted() {},
    computed: {
        isPlanSent: function() {
            return JSON.stringify(this.$store.state.plans) === JSON.stringify(this.$store.state.sentPlans)
        }
    },
    props: ['hakoniwa', 'island'],
};
</script>

<style lang="scss" scoped>

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
