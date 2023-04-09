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
                        v-for="plan of planList"
                        :key="plan.key"
                        :value="plan.key"
                    > {{ plan.name }} {{ plan.priceString }} </option>
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
                    <option v-for="x of hakoniwa.height" :key="x" :value="x-1">
                        {{ x-1 }}
                    </option>
                </select>
            </span>
            <span style="vertical-align: middle">
                ,
            </span>
            <span class="select is-small">
                <select v-model="$store.state.selectedPoint.y">
                    <option v-for="y of hakoniwa.height" :key="y" :value="y-1">
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
                <select v-model="selectedAmount">
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
                    <option :value="island.name"> {{ island.name }} 島 </option>
                </select>
            </div>
        </div>

        <hr/>

        <div>
            <span class="is-small" style="vertical-align: middle">
                コマンド移動：
            </span>
            <button class="button is-small" @click="onClickMoveUp"> ▲ </button> - <button class="button is-small" @click="onClickMoveDown"> ▼ </button>
        </div>

        <hr />

        <div>
            <button :class="['button','is-small','is-primary', this.$store.state.isSendingPlan ? 'is-loading' : '']" @click="onClickSendPlan">計画送信</button>
        </div>
    </div>
</template>

<script lang="ts">

import { getDefaultPlan, Plan } from "../store/Plan";

export default {
    components: {  },
    data() {
        return {
            MAX_PLAN_NUMBER: 30,
            selectedPlan: 'cash_flow',
            selectedAmount: 1,
            selectedTargetIsland: this.island.name,
        }
    },
    setup() {},
    methods: {
        getSelectedPlan(): Plan {
            return {
                key: this.selectedPlan,
                data: {
                    name: this.planList[this.selectedPlan].name,
                    point: {
                        x: this.$store.state.selectedPoint.x,
                        y: this.$store.state.selectedPoint.y,
                    },
                    amount: this.selectedAmount,
                    usePoint: this.planList[this.selectedPlan].usePoint,
                }
            };
        },
        onClickInsert() {
            this.$store.state.plan.splice(this.$store.state.selectedPlanNumber-1, 0, this.getSelectedPlan());
            this.$store.state.plan.pop();
            if (this.$store.state.selectedPlanNumber < this.MAX_PLAN_NUMBER) {
                this.$store.state.selectedPlanNumber++;
            }
        },
        onClickOverwrite() {
            this.$store.state.plan[this.$store.state.selectedPlanNumber-1] = this.getSelectedPlan();

            if (this.$store.state.selectedPlanNumber < this.MAX_PLAN_NUMBER) {
                this.$store.state.selectedPlanNumber++;
            }
        },
        onClickDelete() {
            this.$store.state.plan.splice(this.$store.state.selectedPlanNumber-1, 1);
            this.$store.state.plan.push(getDefaultPlan());
        },
        onClickMoveUp() {
            console.log(this.selectedPlan)
        },
        onClickMoveDown() {
            console.log(this.selectedPlan)
        },
        onClickSendPlan() {
            this.$store.state.isSendingPlan = true
            this.$store.dispatch('sendPlan')
        }
    },
    mounted() {
    },
    computed: {},
    props: ['hakoniwa', 'island', 'islandStatus', 'planList'],
};
</script>

<style lang="scss" scoped>

#plan-controller {
    background-color: #e2e8f0;
    margin: 0 10px 16px 10px;
    padding: 10px;
    min-width: 230px;
    max-width: 230px;
    max-height: 496px;
}

hr {
    margin: 8px 0;
}

</style>
