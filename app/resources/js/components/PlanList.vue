<template>
    <div
        id="plan-list"
        :class="[isPlanSent ? 'bg-surface-variant' : 'bg-error-container text-on-error-container']"
    >
        <div class="send-status">
            <div v-if="isPlanSent" class="send-status-text bg-background text-on-background"> -- 計画送信済み --</div>
            <div v-else class="send-status-text bg-error text-on-error"> -- 計画未送信 --</div>
        </div>
        <div class="plans">
            <div
                v-for="(plan, index) of store.plans"
                :key="'list-' + index + '-' + plan.key"
                class="plan"
                @click="onClickPlan(index)"
                :class="{'bg-white/30': index+1 === store.selectedPlanNumber}"
            >
                <!--                :style="[ parseInt(index)+1 === store.selectedPlanNumber ? { textDecoration: 'underline' } : '']"-->
                <span class="plan-index">
                    {{ index + 1 }}
                </span>
                <span class="plan-separator">:</span>
                <div class="plan-desc">
                    <span v-if="
                        plan.data.useTargetIsland &&
                        plan.data.targetIsland !== null &&
                         store.targetIslands.filter((i) => { return i.id === plan.data.targetIsland}).length >= 1">
                        {{ store.targetIslands.filter((i) => { return i.id === plan.data.targetIsland})[0].name }}島 &#x20;
                    </span>
                    <span v-if="plan.data.usePoint">
                        地点 ({{ plan.data.point.x }},{{ plan.data.point.y }})
                    </span>
                    <span v-if="plan.data.usePoint || (plan.data.useTargetIsland &&
                        plan.data.targetIsland !== null &&
                         store.targetIslands.filter((i) => { return i.id === plan.data.targetIsland}).length >= 1)">
                        <span v-if="plan.data.useTargetIsland">へ</span>
                        <span v-else>に</span>
                    </span>
                    <span class="font-bold">
                        {{ plan.data.name }}
                    </span>
                    <span v-if="plan.data.useAmount">
                        <span v-if="plan.data.amount === 0"> {{ plan.data.defaultAmountString }}</span>
                        <span v-else> {{ plan.data.amountString.replace(':amount:', plan.data.amount.toString()) }} </span>
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import {defineComponent} from "vue";
import {useMainStore} from "../store/MainStore";

export default defineComponent({
    data() {
        return {
        }
    },
    setup() {
        const store = useMainStore();
        return { store }
    },
    methods: {
        onClickPlan(index) {
            this.store.selectedPlanNumber = parseInt(index)+1
        }
    },
    mounted() {},
    computed: {
        isPlanSent: function() {
            return JSON.stringify(this.store.plans) === JSON.stringify(this.store.sentPlans)
        }
    },
});
</script>

<style lang="scss" scoped>
#plan-list {
    @apply rounded-xl mx-1 lg:ml-3 mb-3 p-2 w-[45%] max-w-[230px] lg:max-h-[496px] text-left overflow-x-visible overflow-y-scroll drop-shadow-md;

    .send-status {
        @apply w-full text-center text-white mb-2;

        .send-status-text {
            @apply w-full py-1 rounded-2xl;
        }
    }

    .plans {
        @apply text-sm w-full;

        .plan {
            @apply w-full flex;

            .plan-index {
                @apply min-w-[1rem] mr-0.5;
            }

            .plan-separator {
                @apply min-w-[3%] mr-0.5;
            }

            .plan-desc {
                @apply inline-block grow;
            }
        }
    }
}
</style>
