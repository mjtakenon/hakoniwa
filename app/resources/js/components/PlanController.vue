<template>
    <div id="plan-controller">
        <div class="mb-4">
            <div class="section-header">動作</div>
            <div class="action">
                <button class="action-button button-white" @click="onClickInsert">挿入</button>
                <button class="action-button button-white" @click="onClickOverwrite">上書き</button>
                <button class="action-button button-white" @click="onClickDelete">削除</button>
            </div>
        </div>

        <div class="mb-4">
            <div class="section-header">開発計画</div>
            <div class="dev-plan">
                <p class="title-sm-inline">計画番号:</p>
                <select class="float-right" v-model="store.selectedPlanNumber">
                    <option v-for="num of MAX_PLAN_NUMBER" :value="num" :key="num"> {{ num }}</option>
                </select>
            </div>
            <div class="dev-plan">
                <p class="title-sm-block">開発:</p>
                <select class="plan-select-develop" v-model="selectedPlan">
                    <option
                        v-for="plan of store.planCandidate"
                        :key="plan.key"
                        :value="plan.key"
                    > {{ plan.data.name }} {{ plan.data.priceString }} </option>
                    <option
                        value="all_grading"
                    > 全荒地に整地入力 (荒地x5億円) </option>
                    <option
                        value="all_ground_leveling"
                    > 全荒地に高速整地入力 (荒地x100億円) </option>
                    <option
                        value="all_excavation"
                    > 全浅瀬に掘削入力 (浅瀬x200億円) </option>
                </select>
            </div>
            <div class="dev-plan">
                <p class="title-sm-block">座標:</p>
                <div class="plan-select-pos">
                    <span>( </span>
                    <span>
                        <select v-model="store.selectedPoint.x">
                            <option v-for="x of store.hakoniwa.height" :key="x" :value="x-1">
                                {{ x - 1 }}
                            </option>
                        </select>
                    </span>
                    <span> , </span>
                    <span>
                        <select v-model="store.selectedPoint.y">
                            <option v-for="y of store.hakoniwa.height" :key="y" :value="y-1">
                                {{ y - 1 }}
                            </option>
                        </select>
                    </span>
                    <span> )</span>
                </div>
            </div>
            <div class="dev-plan">
                <p class="title-sm-block">数量</p>
                <select class="float-right" v-model="store.selectedAmount">
                    <option v-for="n of 100" :key="n-1" :value="n-1">
                        {{ n - 1 }}
                    </option>
                </select>
            </div>
            <div class="dev-plan border-t-2 pt-2 border-gray-300">
                <p class="title-block">目標の島:</p>
                <div class="plan-target-island">
                    <select class="target-select" v-model="store.selectedTargetIsland">
                        <option v-for="targetIsland of store.targetIslands" :key="targetIsland.id" :value="targetIsland.id">
                            {{ targetIsland.name }} 島
                        </option>
                    </select>
                    <a class="button-white target-open" :href="'/islands/' + store.selectedTargetIsland" target="_blank"> 開く </a>
                </div>
            </div>
        </div>

        <div class="mb-4">
            <div class="section-header">操作</div>
            <div class="mb-2 px-2 text-left">
                <p class="title-sm-block">コマンド移動</p>
                <div class="move-command">
                    <button class="button-white move-command-button mr-2" @click="onClickMoveUp"
                            :disabled="store.selectedPlanNumber <= 1">▲
                    </button>
                    <button class="button-white move-command-button" @click="onClickMoveDown"
                            :disabled="store.selectedPlanNumber >= MAX_PLAN_NUMBER">▼
                    </button>
                </div>
            </div>
            <div class="send-plan">
                <button
                    class="send-plan-button"
                    :class="{'button-disabled': store.isSendingPlan}"
                    @click="onClickSendPlan">
                    <span v-if="!store.isSendingPlan">計画送信</span>
                    <div
                        v-if="store.isSendingPlan"
                        class="button-circle"
                    >
                        <div class="button-circle-spin"></div>
                    </div>
                </button>

            </div>
        </div>
        <send-notification></send-notification>
    </div>
</template>

<script lang="ts">

import {Plan} from "../store/Entity/Plan";
import SendNotification from "./SendNotification.vue";
import {defineComponent} from "vue";
import {useMainStore} from "../store/MainStore";
import {Point} from "../store/Entity/Point";

export default defineComponent({
    components: {SendNotification},
    data() {
        return {
            MAX_PLAN_NUMBER: 30,
            selectedPlan: 'grading',
        }
    },
    setup() {
        const store = useMainStore()
        return {store}
    },
    methods: {
        getSelectedPlan(): Plan {
            // TODO: IslandEditorと共通化できる
            const result = this.store.planCandidate.find(c => c.key === this.selectedPlan);
            if (result === undefined) return null;
            else {
                const p = result.data;
                return {
                    key: this.selectedPlan,
                    data: {
                        name: p.name,
                        point: {
                            x: this.store.selectedPoint.x,
                            y: this.store.selectedPoint.y
                        },
                        amount: this.store.selectedAmount,
                        usePoint: p.usePoint,
                        useAmount: p.useAmount,
                        useTargetIsland: p.useTargetIsland,
                        targetIsland: this.store.selectedTargetIsland,
                        isFiring: p.isFiring,
                        priceString: p.priceString,
                        amountString: p.amountString,
                        defaultAmountString: p.defaultAmountString
                    }
                }

            }
        },
        getCustomPlan(key: string, point: Point): Plan {
            const result = this.store.planCandidate.find(c => c.key === key);
            if (result === undefined) return null;
            else {
                return {
                    key: key,
                    data: {
                        name: result.data.name,
                        point: {
                            x: point.x,
                            y: point.y,
                        },
                        amount: this.store.selectedAmount,
                        usePoint: result.data.usePoint,
                        useAmount: result.data.useAmount,
                        useTargetIsland: result.data.useTargetIsland,
                        targetIsland: this.store.selectedTargetIsland,
                        isFiring: result.data.isFiring,
                        priceString: result.data.priceString,
                        amountString: result.data.amountString,
                        defaultAmountString: result.data.defaultAmountString,
                    }
                };
            }
        },
        insertPlanAutomatically(source: string, target: string) {
            // targetのコマンドが操作できない場合はreturn
            if(!this.store.planCandidate.find(p => p.key === target)) {
                return;
            }
            for (let terrain of this.store.terrains) {
                if (terrain.type === source) {
                    let plan = this.getCustomPlan(target, terrain.data.point)

                    this.store.plans.splice(this.store.selectedPlanNumber - 1, 0, plan);
                    this.store.plans.pop();
                    if (this.store.selectedPlanNumber < this.MAX_PLAN_NUMBER) {
                        this.store.selectedPlanNumber++;
                    }
                }
            }
        },
        overwritePlanAutomatically(source: string, target: string) {
            // targetのコマンドが操作できない場合はreturn
            if(!this.store.planCandidate.find(p => p.key === target)) {
                return;
            }
            for (let terrain of this.store.terrains) {
                if (terrain.type === source) {
                    this.store.plans[this.store.selectedPlanNumber-1] = this.getCustomPlan(target, terrain.data.point);

                    if (this.store.selectedPlanNumber < this.MAX_PLAN_NUMBER) {
                        this.store.selectedPlanNumber++;
                    }
                }
            }
        },
        onClickInsert() {
            if (this.selectedPlan === 'all_grading') {
                this.insertPlanAutomatically('wasteland','grading');
                return;
            } else if (this.selectedPlan === 'all_ground_leveling') {
                this.insertPlanAutomatically('wasteland','ground_leveling');
                return;
            } else if (this.selectedPlan === 'all_excavation') {
                this.insertPlanAutomatically('shallow','excavation');
                return;
            }

            this.store.plans.splice(this.store.selectedPlanNumber-1, 0, this.getSelectedPlan());
            this.store.plans.pop();
            if (this.store.selectedPlanNumber < this.MAX_PLAN_NUMBER) {
                this.store.selectedPlanNumber++;
            }
        },
        onClickOverwrite() {
            if (this.selectedPlan === 'all_grading') {
                this.overwritePlanAutomatically('wasteland','grading');
                return;
            } else if (this.selectedPlan === 'all_ground_leveling') {
                this.overwritePlanAutomatically('wasteland','ground_leveling');
                return;
            } else if (this.selectedPlan === 'all_excavation') {
                this.overwritePlanAutomatically('shallow','excavation');
                return;
            }

            this.store.plans[this.store.selectedPlanNumber-1] = this.getSelectedPlan();

            if (this.store.selectedPlanNumber < this.MAX_PLAN_NUMBER) {
                this.store.selectedPlanNumber++;
            }
        },
        onClickDelete() {
            this.store.plans.splice(this.store.selectedPlanNumber-1, 1);
            this.store.plans.push(this.store.getDefaultPlan);
        },
        onClickMoveUp() {
            if (this.store.selectedPlanNumber <= 1) {
                return
            }
            [this.store.plans[this.store.selectedPlanNumber-2], this.store.plans[this.store.selectedPlanNumber-1]] = [this.store.plans[this.store.selectedPlanNumber-1], this.store.plans[this.store.selectedPlanNumber-2]];
            this.store.selectedPlanNumber--;
        },
        onClickMoveDown() {
            if (this.store.selectedPlanNumber >= 30) {
                return
            }
            [this.store.plans[this.store.selectedPlanNumber], this.store.plans[this.store.selectedPlanNumber-1]] = [this.store.plans[this.store.selectedPlanNumber-1], this.store.plans[this.store.selectedPlanNumber]];
            if (this.store.selectedPlanNumber < this.MAX_PLAN_NUMBER) {
                this.store.selectedPlanNumber++;
            }
        },
        onClickSendPlan() {
            this.store.isSendingPlan = true;
            this.store.putPlan()
        }
    },
    computed: {},
    props: [],
});
</script>


<style lang="postcss" scoped>
#plan-controller {
    @apply w-[45%] bg-gray-200 rounded-xl mx-1 lg:mr-2 mb-3 p-1 max-lg:h-fit lg:p-2 max-w-[230px] drop-shadow-md;

    .section-header {
        @apply w-full text-left pl-2 text-gray-500 border-gray-500 border-b mb-2 text-sm;
    }

    .action {
        @apply px-1 lg:px-2 grid grid-cols-3 gap-2;

        .action-button {
            @apply p-1 max-lg:text-xs;
        }
    }

    .title-sm-inline {
        @apply inline-block mr-2 max-lg:text-sm;
    }

    .title-sm-block {
        @apply inline-block mr-2 max-lg:text-sm max-lg:mb-0;
    }

    .title-block {
        @apply block mb-1 w-full max-lg:text-sm max-lg:mb-0
    }

    .dev-plan {
        @apply mb-2 lg:mb-3 px-2 text-left;

        .plan-select-develop {
            @apply w-full lg:w-3/4 text-sm lg:float-right;
        }

        .plan-select-pos {
            @apply inline-block max-lg:w-full max-lg:text-center lg:float-right;
        }

        .plan-target-island {
            @apply w-full flex items-center max-lg:flex-wrap max-lg:justify-end;

            .target-select {
                @apply lg:grow lg:mr-2 max-lg:w-full lg:float-right text-sm;
            }

            .target-open {
                @apply px-1 py-0.5 max-lg:mt-1 text-xs lg:text-sm;
            }
        }
    }
    .move-command {
        @apply max-lg:w-full text-center lg:float-right;

        .move-command-button {
            @apply px-1.5 py-0.5 text-sm;
        }
    }

    .send-plan {
        @apply mt-8 lg:mt-4 px-2;

        .send-plan-button {
            @apply py-1 button-primary w-2/3 lg:w-1/2;
        }

        .button-circle {
            @apply flex justify-center;

            .button-circle-spin {
                @apply animate-spin m-1 w-4 h-4 border-2 border-white rounded-full border-t-transparent;
            }
        }
    }
}

</style>
