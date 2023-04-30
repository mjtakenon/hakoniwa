<template>
    <div id="plan-controller" class="box">

        <div> 動作 </div>
        <div class="is-flex">
            <div class="is-flex-direction-row is-flex-grow-1"><button class="button is-small" @click="onClickInsert">挿入</button></div>
            <div class="is-flex-direction-row is-flex-grow-1"><button class="button is-small" @click="onClickOverwrite">上書き</button></div>
            <div class="is-flex-direction-row is-flex-grow-1"><button class="button is-small" @click="onClickDelete">削除</button></div>
        </div>

        <hr />

        <div>
            <span style="vertical-align: middle">
                計画番号：
            </span>
            <div class="select is-small">
                <select v-model="$store.state.selectedPlanNumber">
                    <option v-for="num of MAX_PLAN_NUMBER" :value="num" :key="num"> {{ num }} </option>
                </select>
            </div>
        </div>

        <hr/>

        <div>
            開発計画 <br/>
            <div class="select is-small">
                <select v-model="selectedPlan">
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
        </div>

        <hr/>

        <div>
            <span style="vertical-align: middle">
                座標：（
            </span>
            <span class="select is-small">
                <select v-model="$store.state.selectedPoint.x">
                    <option v-for="x of $store.state.hakoniwa.height" :key="x" :value="x-1">
                        {{ x-1 }}
                    </option>
                </select>
            </span>
            <span style="vertical-align: middle">
                ,
            </span>
            <span class="select is-small">
                <select v-model="$store.state.selectedPoint.y">
                    <option v-for="y of $store.state.hakoniwa.height" :key="y" :value="y-1">
                        {{ y-1 }}
                    </option>
                </select>
            </span>
            <span style="vertical-align: middle">
                ）
            </span>
        </div>

        <hr/>

        <div>
            <span style="vertical-align: middle">
                数量：
            </span>
            <div class="select is-small">
                <select v-model="$store.state.selectedAmount">
                    <option v-for="n of 99" :key="n" :value="n">
                        {{ n }}
                    </option>
                </select>
            </div>
        </div>

        <hr/>

        <div>
            目標の島 <br/>
            <div class="select is-small">
                <select v-model="selectedTargetIsland">
                    <option :value="$store.state.island.name"> {{ $store.state.island.name }} 島 </option>
                </select>
            </div>
        </div>

        <hr/>

        <div>
            <span class="is-small" style="vertical-align: middle">
                コマンド移動：
            </span>
            <button class="button is-small" @click="onClickMoveUp" :disabled="this.$store.state.selectedPlanNumber <= 1"> ▲ </button> - <button class="button is-small" @click="onClickMoveDown" :disabled="this.$store.state.selectedPlanNumber >= MAX_PLAN_NUMBER"> ▼ </button>
        </div>

        <hr />

        <div>
            <button :class="['button','is-small','is-primary', this.$store.state.isSendingPlan ? 'is-loading' : '']" @click="onClickSendPlan">計画送信</button>
        </div>
        <div class="pt-2"></div>
        <send-notification></send-notification>
    </div>
</template>

<script lang="ts">

import { getDefaultPlan, Plan } from "../store/Entity/Plan";
import SendNotification from "./SendNotification.vue";

export default {
    components: {SendNotification},
    data() {
        return {
            MAX_PLAN_NUMBER: 30,
            selectedPlan: 'grading',
            selectedTargetIsland: this.$store.state.island.name,
        }
    },
    setup() {},
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
                    priceString: ''
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
                    priceString: ''
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
                    let plan = this.getCustomPlan(target, terrain.data.point)

                    this.$store.state.plans[this.$store.state.selectedPlanNumber-1] = plan;

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
};
</script>

<style lang="scss" scoped>

#plan-controller {
    background-color: #e2e8f0;
    margin: 0 10px 16px 10px;
    padding: 10px;
    min-width: 230px;
    max-width: 230px;
    //max-height: 496px;
}

hr {
    margin: 8px 0;
}

</style>
