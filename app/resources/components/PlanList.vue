<template>
    <div id="plan-list" class="box">
        <div class="list-header">
            -- 開発計画 --
        </div>
        <table class="list-body">
            <tbody>
                    <tr
                        v-for="[index, plan] of Object.entries(JSON.parse(islandPlans.plan))"
                        key="islandPlan.id"
                    >
                        <td>{{ index }}</td>
                        <td>：</td>
                        <td>{{ plan.data.name }}</td>
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
    },
    mounted() {
        // console.log(this.islandPlans)
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

</style>
