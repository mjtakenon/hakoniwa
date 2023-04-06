<template>
    <div id="plan-list" class="box">
        <div class="list-header">
            -- 開発計画 --
        </div>
<!--        <div v-for="[index, plan] of Object.entries(JSON.parse(islandPlans.plan))" key="islandPlan.id">-->
        <table>
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
<!--        </div>-->
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
    //font-size: 14px;
}

.list-header {
    text-align: center;
}

</style>
