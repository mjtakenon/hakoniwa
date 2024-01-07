<template>
    <div v-show="store.showPlanWindow" class="plan-window"
         :style="[
                 { top: store.planWindowY + 'px'}, { left: store.planWindowX + 'px'}
             ]"
    >
        <div class="plan-window-header">
            <div class="grow px-3">
                <span class="mr-2">({{ store.selectedPoint.x }},{{ store.selectedPoint.y }})</span>
                <span class="text-xs">計画番号: </span>
                <span class="mr-1">{{ store.selectedPlanNumber }}</span>
            </div>

            <button
                class="plan-window-close"
                @click="onClickClosePlan"
            >×
            </button>
        </div>
        <div
            v-for="plan of planCandidate"
            :key="plan.key"
            class="plan-window-select"
        >
            <div @click="onClickPlan(plan.key)">
                <a class="action-name">{{ plan.data.name }}</a>
                <span class="action-price">{{ plan.data.priceString }}</span>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import {useMainStore} from "../../../store/MainStore";
import {Plan} from "../../../store/Entity/Plan";
import {computed} from "vue";

const store = useMainStore()

const MAX_PLAN_NUMBER = 30

const planCandidate = computed(() => {
    return store.planCandidate.filter(p => store.isOpenPopup ? p.data.usePoint && p.data.useTargetIsland : p.data.usePoint)
})

const onClickClosePlan = () => {
    store.showPlanWindow = false;
}

const onClickPlan = (key) => {
    store.plans.splice(store.selectedPlanNumber - 1, 0, getSelectedPlan(key));
    store.plans.pop();
    if (store.selectedPlanNumber < MAX_PLAN_NUMBER) {
        store.selectedPlanNumber++;
    }
    store.showPlanWindow = false;
}

const getSelectedPlan = (key): Plan => {
    const result = store.planCandidate.find(c => c.key === key);
    if (result === undefined) return null;
    else {
        const p = result.data;
        return {
            key: key,
            data: {
                name: p.name,
                point: {
                    x: store.selectedPoint.x,
                    y: store.selectedPoint.y
                },
                amount: store.selectedAmount,
                usePoint: p.usePoint,
                useAmount: p.useAmount,
                useTargetIsland: p.useTargetIsland,
                targetIsland: store.selectedTargetIsland,
                isFiring: p.isFiring,
                priceString: p.priceString,
                amountString: p.amountString,
                defaultAmountString: p.defaultAmountString
            }
        }
    }
}
</script>

<style lang="scss" scoped>

.plan-window {
    @apply block absolute;
    @apply bg-surface-variant text-on-surface-variant w-fit max-lg:min-w-[230px] max-lg:max-w-[230px] max-lg:-translate-x-1/2 lg:max-w-[240px] rounded-md drop-shadow-xl text-left overflow-hidden max-md:text-sm border border-primary dark:border-primary-container z-30;
    @apply animate-fadein;

    .plan-window-header {
        @apply flex p-0 m-0 bg-primary dark:bg-primary-container text-on-primary dark:text-on-primary-container items-center;

        .plan-window-close {
            @apply inline-block bg-primary dark:bg-primary-container text-on-primary dark:text-on-primary-container p-0 border-none hover:bg-primary hover:dark:bg-primary-container drop-shadow-none mr-3;
        }
    }

    .plan-window-select {
        @apply w-full px-2 max-md:py-1 hover:bg-on-primary;

        .action-name {
            @apply inline-block font-bold text-sm md:text-base mr-1;
        }

        .action-price {
            @apply inline-block text-xs;
        }

        &:not(:last-child) {
            @apply border-b border-gray-700;
        }
    }
}
</style>
