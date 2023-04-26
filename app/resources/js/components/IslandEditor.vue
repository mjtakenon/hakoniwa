<template>
    <div id="island">
        <div class="row m-0 p-0" v-for="y of $store.state.hakoniwa.height" :key="y">
            <div class="right-padding" v-if="y%2 === 1">
                {{ y-1 }}
            </div>
            <div class="cell" v-for="x of $store.state.hakoniwa.width" :key="x">
                <img
                    @mouseover="onMouseOverCell(x-1, y-1)"
                    @mouseleave="onMouseLeaveCell()"
                    @click="onClickCell(x-1, y-1, $event)"
                    :src="getIslandTerrain(x-1,y-1).data.image_path"
                    :alt="getIslandTerrain(x-1,y-1).type"
                    :class="[
                        'cell',
                        isSelectedCell(x-1, y-1) && this.showPlanWindow ? 'cell-is-selected' : '',
                        isReferencedCell(x-1, y-1) ? 'cell-is-referenced' : '',
                    ]"
                >
            </div>
            <div class="left-padding" v-if="y%2 === 0"></div>
        </div>
        <div v-show="showHoverWindow" class="hover-window" :style="{ top: hoverWindowTop+'px', left: hoverWindowLeft+'px' }">
            <div class="is-flex">
                <img
                    class="is-flex-direction-column hover-window-img"
                    :src="getIslandTerrain(hoverCellPoint.x, hoverCellPoint.y).data.image_path"
                >
                <div class="is-flex-direction-column hover-window-info">
                    {{ (getIslandTerrain(hoverCellPoint.x, hoverCellPoint.y).data.info) }}
                    <div v-for="(plan, index) of $store.state.plans">
                        <div v-if="plan.data.point.x === hoverCellPoint.x && plan.data.point.y === hoverCellPoint.y && plan.data.usePoint">
                            <span>[{{ index + 1 }}] </span>
                            <span>{{ plan.data.name }}</span>
                            <span v-if="plan.data.amount >= 2"> ({{ plan.data.amount }}回実施)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-show="showPlanWindow" class="plan-window card" :style="{ top: planWindowTop+'px', left: planWindowLeft+'px' }">
            <button
                class="delete is-large plan-window-delete"
                @click="onClickClosePlan"
            ></button>
            <div
                v-for="plan of $store.state.planCandidate"
                :key="plan.key"
            >
                <a @click="onClickPlan(plan.key)">
                    <div style="border: 1px solid lightgray; padding: 2px 4px;">
                        {{ plan.name }} {{ plan.priceString }}
                    </div>
                </a>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Terrain } from "../store/Entity/Terrain";
import {Plan} from "../store/Entity/Plan";

export default {
    components: {},
    data() {
        return {
            MAX_PLAN_NUMBER: 30,
            showHoverWindow: false,
            showPlanWindow: false,
            hoverCellPoint: {
                "x": 0,
                "y": 0,
            },
            hoverWindowTop: 170,
            hoverWindowLeft: 0,
            planWindowTop: 0,
            planWindowLeft: 0,
        }
    },
    setup() {
    },
    methods: {
        getIslandTerrain(x, y): Terrain {
            return this.$store.state.terrains.filter(function(item, idx){
                if (item.data.point.x === x && item.data.point.y === y) return true;
            }).pop();
        },
        onMouseOverCell(x, y) {
            this.showHoverWindow = true;
            this.hoverCellPoint.x = x;
            this.hoverCellPoint.y = y;

            // 左半分
            if (this.hoverCellPoint.x < this.$store.state.hakoniwa.width / 2) {
                this.hoverWindowLeft = 250;
            } else {
                this.hoverWindowLeft = 0;
            }
        },
        onMouseLeaveCell() {
            this.showHoverWindow = false;
        },
        onClickCell(x, y, event) {
            if (this.showPlanWindow &&
                this.$store.state.selectedPoint.x === x &&
                this.$store.state.selectedPoint.y === y
            ) {
                this.showPlanWindow = false;
                return;
            }
            this.$store.state.selectedPoint.x = x;
            this.$store.state.selectedPoint.y = y;
            this.showPlanWindow = true;

            // TODO: layerは使わないようにする
            this.planWindowTop = event.layerY;
            this.planWindowLeft = event.layerX;
        },
        onClickPlan(key) {
            this.$store.state.plans.splice(this.$store.state.selectedPlanNumber-1, 0, this.getSelectedPlan(key));
            this.$store.state.plans.pop();
            if (this.$store.state.selectedPlanNumber < this.MAX_PLAN_NUMBER) {
                this.$store.state.selectedPlanNumber++;
            }
            this.showPlanWindow = false;
        },
        getSelectedPlan(key): Plan {
            return {
                key: key,
                data: {
                    name: this.$store.state.planCandidate[key].name,
                    point: {
                        x: this.$store.state.selectedPoint.x,
                        y: this.$store.state.selectedPoint.y,
                    },
                    amount: this.$store.state.selectedAmount,
                    usePoint: this.$store.state.planCandidate[key].usePoint,
                    useAmount: this.$store.state.planCandidate[key].useAmount,
                    useTargetIsland: this.$store.state.planCandidate[key].useTargetIsland,
                    priceString: ''
                }
            };
        },
        onClickClosePlan() {
            this.showPlanWindow = false;
        },
    },
    mounted() {
        // console.log(this.$props)
    },
    computed: {
        isSelectedCell() {
            return (x, y) => {
                return x === this.$store.state.selectedPoint.x && y === this.$store.state.selectedPoint.y
            }
        },
        isReferencedCell() {
            return (x, y) => {
                let referencedPlan = this.$store.state.plans[this.$store.state.selectedPlanNumber-1]
                return x === referencedPlan.data.point.x && y === referencedPlan.data.point.y && referencedPlan.data.usePoint
            }
        },
    },
    props: [],
};
</script>

<style lang="scss" scoped>

#island {
    position: relative;
    margin: 0 auto;
    max-width: 480px;
    min-width: 496px;
    min-height: 496px;
}

.row {
    display: grid;
    grid-template-columns: repeat(16, 1fr);
}

.cell {
    width: 32px;
    height: 32px;
}

.cell-is-selected {
    border: 1px solid white;
}

.cell-is-referenced {
    border: 1px solid red;
    //animation: fadeout-keyframes 1s ease 0s 1 forwards;
}

//@keyframes fadeout-keyframes {
//    0% {
//        opacity: 0.8;
//    }
//
//    100% {
//        opacity: 1;
//    }
//}

.left-padding {
    width: 16px;
    height: 32px;
    background-image: url("/img/hakoniwa/hakogif/land0.gif");
    background-position: left;
}

.right-padding {
    width: 16px;
    height: 32px;
    background-image: url("/img/hakoniwa/hakogif/land0.gif");
    background-position: right;

    color: white;
    font-size: 10px;
    padding-top: 8px;
}

.hover-window {
    text-align: left;
    padding: 10px;
    margin: 10px;
    position: absolute;
    border: 1px solid;
    background-color: lightyellow;
    min-width: 200px;
    min-height: 50px;
}

.hover-window-img {
    width:32px;
    height:32px;
    margin-right: 10px;
}

.hover-window-info {
    white-space: pre-line;
}

.plan-window {
    text-align: left;
    position: absolute;
    padding: 10px;
    margin: 10px;
    min-width: 240px;
    background-color: white;
    overflow: hidden;
}

.plan-window-delete {
    position: absolute;
    right: 10px;
    color: white;
}

</style>
