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
                <select class="float-right" v-model="$store.state.selectedPlanNumber">
                    <option v-for="num of MAX_PLAN_NUMBER" :value="num" :key="num"> {{ num }}</option>
                </select>
            </div>
            <div class="dev-plan">
                <p class="title-sm-block">開発:</p>
                <select class="plan-select-develop" v-model="selectedPlan">
                    <option
                        v-for="plan of $store.state.planCandidate"
                        :key="plan.key"
                        :value="plan.key"
                    > {{ plan.name }} {{ plan.priceString }} </option>
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
                        <select v-model="$store.state.selectedPoint.x">
                            <option v-for="x of $store.state.hakoniwa.height" :key="x" :value="x-1">
                                {{ x - 1 }}
                            </option>
                        </select>
                    </span>
                    <span> , </span>
                    <span>
                        <select v-model="$store.state.selectedPoint.y">
                            <option v-for="y of $store.state.hakoniwa.height" :key="y" :value="y-1">
                                {{ y - 1 }}
                            </option>
                        </select>
                    </span>
                    <span> )</span>
                </div>
            </div>
            <div class="dev-plan">
                <p class="title-sm-block">数量</p>
                <select class="float-right" v-model="$store.state.selectedAmount">
                    <option v-for="n of 100" :key="n-1" :value="n-1">
                        {{ n - 1 }}
                    </option>
                </select>
            </div>
            <div class="dev-plan border-t-2 pt-2 border-gray-300">
                <p class="title-block">目標の島:</p>
                <div class="plan-target-island">
                    <select class="target-select" v-model="$store.state.selectedTargetIsland">
                        <option v-for="targetIsland of $store.state.targetIslands" :key="targetIsland.id" :value="targetIsland.id">
                            {{ targetIsland.name }} 島
                        </option>
                    </select>
                    <a class="button-white target-open" :href="'/islands/' + $store.state.selectedTargetIsland" target="_blank"> 開く </a>
                </div>
            </div>
        </div>

        <div class="mb-4">
            <div class="section-header">操作</div>
            <div class="mb-2 px-2 text-left">
                <p class="title-sm-block">コマンド移動</p>
                <div class="move-command">
                    <button class="button-white move-command-button mr-2" @click="onClickMoveUp"
                            :disabled="this.$store.state.selectedPlanNumber <= 1">▲
                    </button>
                    <button class="button-white move-command-button" @click="onClickMoveDown"
                            :disabled="this.$store.state.selectedPlanNumber >= MAX_PLAN_NUMBER">▼
                    </button>
                </div>
            </div>
            <div class="send-plan">
                <button
                    class="send-plan-button"
                    :class="{'button-disabled': this.$store.state.isSendingPlan}"
                    @click="onClickSendPlan">
                    <span v-if="!this.$store.state.isSendingPlan">計画送信</span>
                    <div
                        v-if="this.$store.state.isSendingPlan"
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

import {getDefaultPlan, Plan} from "../store/Entity/Plan";
import SendNotification from "./SendNotification.vue";
import {defineComponent} from "vue";

export default defineComponent({
    components: {SendNotification},
    data() {
        return {
            MAX_PLAN_NUMBER: 30,
            selectedPlan: 'grading',
        }
    },
    methods: {
        getSelectedPlan(): Plan {
            return {
                key: this.selectedPlan,
                data: {
                    name: this.$store.state.planCandidate[this.selectedPlan].name,
                    point: {
                        x: this.$store.state.selectedPoint.x,
                        y: this.$store.state.selectedPoint.y,
                    },
                    amount: this.$store.state.selectedAmount,
                    usePoint: this.$store.state.planCandidate[this.selectedPlan].usePoint,
                    useAmount: this.$store.state.planCandidate[this.selectedPlan].useAmount,
                    useTargetIsland: this.$store.state.planCandidate[this.selectedPlan].useTargetIsland,
                    targetIsland: this.$store.state.selectedTargetIsland,
                    isFiring: this.$store.state.planCandidate[this.selectedPlan].isFiring,
                    priceString: this.$store.state.planCandidate[this.selectedPlan].priceString,
                    amountString: this.$store.state.planCandidate[this.selectedPlan].amountString,
                    defaultAmountString: this.$store.state.planCandidate[this.selectedPlan].defaultAmountString,
                }
            };
        },
        getCustomPlan(key: string, point: Point): Plan {
            return {
                key: key,
                data: {
                    name: this.$store.state.planCandidate[key].name,
                    point: {
                        x: point.x,
                        y: point.y,
                    },
                    amount: this.$store.state.selectedAmount,
                    usePoint: this.$store.state.planCandidate[key].usePoint,
                    useAmount: this.$store.state.planCandidate[key].useAmount,
                    useTargetIsland: this.$store.state.planCandidate[key].useTargetIsland,
                    targetIsland: this.$store.state.selectedTargetIsland,
                    isFiring: this.$store.state.planCandidate[key].isFiring,
                    priceString: this.$store.state.planCandidate[key].priceString,
                    amountString: this.$store.state.planCandidate[key].amountString,
                    defaultAmountString: this.$store.state.planCandidate[key].defaultAmountString,
                }
            };
        },
        insertPlanAutomatically(source: string, target: string) {
            for (let terrain of this.$store.state.terrains) {
                if (terrain.type === source) {
                    let plan = this.getCustomPlan(target, terrain.data.point)

                    this.$store.state.plans.splice(this.$store.state.selectedPlanNumber-1, 0, plan);
                    this.$store.state.plans.pop();
                    if (this.$store.state.selectedPlanNumber < this.MAX_PLAN_NUMBER) {
                        this.$store.state.selectedPlanNumber++;
                    }
                }
            }
        },
        overwritePlanAutomatically(source: string, target: string) {
            for (let terrain of this.$store.state.terrains) {
                if (terrain.type === source) {
                    this.$store.state.plans[this.$store.state.selectedPlanNumber-1] = this.getCustomPlan(target, terrain.data.point);

                    if (this.$store.state.selectedPlanNumber < this.MAX_PLAN_NUMBER) {
                        this.$store.state.selectedPlanNumber++;
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

            this.$store.state.plans.splice(this.$store.state.selectedPlanNumber-1, 0, this.getSelectedPlan());
            this.$store.state.plans.pop();
            if (this.$store.state.selectedPlanNumber < this.MAX_PLAN_NUMBER) {
                this.$store.state.selectedPlanNumber++;
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

            this.$store.state.plans[this.$store.state.selectedPlanNumber-1] = this.getSelectedPlan();

            if (this.$store.state.selectedPlanNumber < this.MAX_PLAN_NUMBER) {
                this.$store.state.selectedPlanNumber++;
            }
        },
        onClickDelete() {
            this.$store.state.plans.splice(this.$store.state.selectedPlanNumber-1, 1);
            this.$store.state.plans.push(getDefaultPlan());
        },
        onClickMoveUp() {
            if (this.$store.state.selectedPlanNumber <= 1) {
                return
            }
            [this.$store.state.plans[this.$store.state.selectedPlanNumber-2], this.$store.state.plans[this.$store.state.selectedPlanNumber-1]] = [this.$store.state.plans[this.$store.state.selectedPlanNumber-1], this.$store.state.plans[this.$store.state.selectedPlanNumber-2]];
            this.$store.state.selectedPlanNumber--;
        },
        onClickMoveDown() {
            if (this.$store.state.selectedPlanNumber >= 30) {
                return
            }
            [this.$store.state.plans[this.$store.state.selectedPlanNumber], this.$store.state.plans[this.$store.state.selectedPlanNumber-1]] = [this.$store.state.plans[this.$store.state.selectedPlanNumber-1], this.$store.state.plans[this.$store.state.selectedPlanNumber]];
            if (this.$store.state.selectedPlanNumber < this.MAX_PLAN_NUMBER) {
                this.$store.state.selectedPlanNumber++;
            }
        },
        onClickSendPlan() {
            this.$store.state.isSendingPlan = true
            this.$store.dispatch('putPlan')
        }
    },
    mounted() {
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
