<template>
    <div class="popup" :class="{'active' : store.isOpenPopup}">
        <div class="popup-background" @click="closePopup"></div>
        <div class="popup-window">
            <div class="popup-window-header">
                <div class="popup-title-target">target:</div>
                <div class="popup-island-name" :class="titleStyle">
                        {{ targetIslandName }}島
                </div>
                <button class="close-button" @click="closePopup">
                    ×
                </button>
            </div>
            <div v-if="store.isLoadingTerrain" class="loading">
                <svg aria-hidden="true" class="loading-circle" viewBox="0 0 100 101" fill="none"
                     xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                        fill="currentColor"/>
                    <path
                        d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                        fill="currentFill"/>
                </svg>
            </div>
            <div v-else id="popup-island">
                <div
                    class="row"
                    v-for="y of store.hakoniwa.height"
                    :key="y"
                >
                    <div class="right-padding" :class="{'opacity-80': this.showPlanWindow}" v-if="y%2 === 1">
                        <span class="right-padding-text">{{ y - 1 }}</span>
                    </div>
                    <div class="cell" v-for="x of store.hakoniwa.width" :key="x">
                        <img
                            @mouseover="onMouseOverCell(x-1, y-1, $event)"
                            @mouseleave="onMouseLeaveCell()"
                            @click="onClickCell(x-1, y-1, $event)"
                            :src="getIslandTerrainImage(x-1,y-1)"
                            :alt="getIslandTerrainInfo(x-1,y-1)"
                            :class="[
                                'cell',
                                isSelectedCell(x-1, y-1) && this.showPlanWindow ? 'cell-is-selected' : '',
                                !isSelectedCell(x-1, y-1) && this.showPlanWindow ? 'opacity-80' : '',
                            ]"
                        >
                    </div>
                    <div class="left-padding" :class="{'opacity-80': this.showPlanWindow}" v-if="y%2 === 0"></div>
                </div>
                <div v-show="showHoverWindow" class="hover-window" :style="{ bottom: hoverWindowY+'px', left: hoverWindowX+'px' }">
                    <div class="hover-window-header">
                        <img
                            class="hover-window-img"
                            :src="getIslandTerrainImage(hoverCellPoint.x, hoverCellPoint.y)"
                        >
                        <div class="grow items-center hover-window-info">
                            {{ (getIslandTerrainInfo(hoverCellPoint.x, hoverCellPoint.y)) }}
                        </div>
                    </div>
                </div>
                <div v-show="showPlanWindow" class="plan-window"
                     :style="[
                 { top: planWindowY + 'px'}, { left: planWindowX + 'px'}
             ]"
                >
                    <div class="plan-window-header">
                        <div class="grow px-3">
                            <span class="mr-2">({{selectedPoint.x}},{{selectedPoint.y}})</span>
                            <span class="text-xs">計画番号: </span>
                            <span class="mr-1">{{store.selectedPlanNumber}}</span>
                        </div>

                        <button
                            class="plan-window-close"
                            @click="onClickClosePlan"
                        >×</button>
                    </div>
                    <div
                        v-for="plan of this.store.planCandidate.filter(p => p.data.usePoint && p.data.useTargetIsland)"
                        :key="plan.key"
                        class="plan-window-select"
                    >
                        <div @click="onClickPlan(plan.key)">
                            <a class="action-name">{{ plan.data.name }}</a>
                            <span class="action-price">{{ plan.data.priceString }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="comment-box">
                <div class="comment-title">
                    Comment:
                </div>
                <div class="comment-text">
                    {{islandComment}}
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import {defineComponent} from "vue";
import IslandViewer from "./IslandViewer.vue";
import {useMainStore} from "../store/MainStore";
import {storeToRefs} from "pinia";
import {Terrain} from "../store/Entity/Terrain";
import {Point} from "../store/Entity/Point";
import {Plan} from "../store/Entity/Plan";

export default defineComponent({
    components: {
        IslandViewer,
    },
    data() {
        return {
            MAX_PLAN_NUMBER: 30,
            targetIslandName: "",
            targetIslandComment: "",
            targetTerrains: [] as Terrain[],
            showHoverWindow: false,
            showPlanWindow: false,
            hoverCellPoint: {
                "x": 0,
                "y": 0,
            } as Point,
            hoverWindowY: 170,
            hoverWindowX: 0,
            screenWidth: document.documentElement.clientWidth,
            planWindowY: 0,
            planWindowX: 0,
            isMobile: (document.documentElement.clientWidth < 1024),
            selectedPoint: {
                x: 0, y:0
            } as Point,
        }
    },
    setup() {
        const store = useMainStore();
        const {isOpenPopup, isLoadingTerrain} = storeToRefs(store);
        return {store, isOpenPopup, isLoadingTerrain};
    },
    mounted() {
        window.addEventListener("resize", this.onWindowSizeChanged);
    },
    unmounted() {
        window.removeEventListener("resize", this.onWindowSizeChanged);
    },
    watch: {
        isOpenPopup() {
            this.targetIslandName = this.store.selectedTargetIslandName;
            if(this.store.isOpenPopup) {
                document.addEventListener("wheel", this.preventScroll, {passive: false});
                document.addEventListener("touchmove", this.preventScroll, {passive: false});
            } else {
                document.removeEventListener("wheel", this.preventScroll);
                document.removeEventListener("touchmove", this.preventScroll);
            }
        },
        isLoadingTerrain() {
            if (this.store.isLoadingTerrain) return;
            const target = this.store.targetIslands.filter(island => island.id === this.store.selectedTargetIsland);
            if (target.length < 1) throw new Error("対象の島が見つかりません");
            if (target[0].terrains === undefined) throw new Error("目標の島に地形情報がありません");
            this.targetTerrains = target[0].terrains;
            this.targetIslandComment = target[0].comment;
        }
    },
    computed: {
        isSelectedCell() {
            return (x, y) => {
                if (this.selectedPoint === null) {
                    return false;
                }
                return x === this.selectedPoint.x && y === this.selectedPoint.y
            }
        },
        titleStyle() {
            if(this.targetIslandName.length > 16) {
                return 'text-[0.5rem] lg:text-sm';
            }
            return 'text-base lg:text-lg'
        },
        hasComment() {
            return this.targetIslandComment === null　|| this.targetIslandComment === undefined || this.targetIslandComment === "";
        },
        islandComment() {
            if (this.hasComment) {
                return "コメントはありません"
            } else {
                return this.targetIslandComment;
            }
        }
    },
    methods: {
        closePopup() {
            this.onMouseLeaveCell();
            this.onClickClosePlan();
            this.store.isOpenPopup = false;
        },
        preventScroll(event: MouseEvent | TouchEvent) {
            event.preventDefault();
        },
        getIslandTerrainImage(x, y): string {
            if (this.targetTerrains.length < 1) return "";
            return this.targetTerrains.filter(item => {
                if (item.data.point.x === x && item.data.point.y === y) return true;
            }).pop().data.image_path;
        },
        getIslandTerrainInfo(x, y): string {
            if (this.targetTerrains.length < 1) return "";
            return this.targetTerrains.filter(item => {
                if (item.data.point.x === x && item.data.point.y === y) return true;
            }).pop().data.info;
        },
        onMouseOverCell(x, y, event: MouseEvent) {
            const offsetY = 25;
            this.hoverWindowY = document.documentElement.clientHeight - event.clientY + offsetY;
            this.hoverWindowX = event.clientX;

            // Screen Overflow Check
            if(this.isMobile) {
                const elementWidth = 200;
                const paddingOffset = 20;
                const leftEdge = this.hoverWindowX - (elementWidth/2);
                const rightEdge = this.hoverWindowX + (elementWidth/2);
                if (leftEdge < paddingOffset) {
                    this.hoverWindowX += (-leftEdge) + paddingOffset;
                }
                else if (rightEdge > this.screenWidth) {
                    this.hoverWindowX -= (rightEdge-this.screenWidth) + paddingOffset;
                }
            }

            this.showHoverWindow = true;
            this.hoverCellPoint.x = x;
            this.hoverCellPoint.y = y;
        },
        onClickPlan(key) {
            this.store.plans.splice(this.store.selectedPlanNumber-1, 0, this.getSelectedPlan(key));
            this.store.plans.pop();
            if (this.store.selectedPlanNumber < this.MAX_PLAN_NUMBER) {
                this.store.selectedPlanNumber++;
            }
            this.showPlanWindow = false;
        },
        getSelectedPlan(key): Plan {
            const result = this.store.planCandidate.find(c => c.key === key);
            if (result === undefined) return null;
            else {
                const p = result.data;
                return {
                    key: key,
                    data: {
                        name: p.name,
                        point: {
                            x: this.selectedPoint.x,
                            y: this.selectedPoint.y
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
        onMouseLeaveCell() {
            this.showHoverWindow = false;
        },
        onClickCell(x, y, event: MouseEvent) {
            if (this.showPlanWindow &&
                this.selectedPoint.x === x &&
                this.selectedPoint.y === y
            ) {
                this.showPlanWindow = false;
                return;
            }
            this.selectedPoint = {x: x, y: y};
            this.showPlanWindow = true;

            if(this.isMobile) {
                this.planWindowX = event.clientX;
                const offsetX = 15;
                const offsetY = 30;
                const elementWidth = 230;
                const leftEdge = this.planWindowX - (elementWidth/2);
                const rightEdge = this.planWindowX + (elementWidth/2);
                if (leftEdge < offsetX) {
                    this.planWindowX += (-leftEdge) + offsetX;
                }
                else if (rightEdge > this.screenWidth) {
                    this.planWindowX -= (rightEdge-this.screenWidth) + offsetX;
                }
                this.planWindowY = event.clientY + offsetY;
            }
            else {
                const offset = 15;
                this.planWindowX = event.clientX + offset;
                this.planWindowY = event.clientY + offset;
            }
        },
        onClickClosePlan() {
            this.showPlanWindow = false;
        },
        onWindowSizeChanged() {
            const newScreenWidth = document.documentElement.clientWidth;
            if (this.screenWidth != newScreenWidth) {
                this.screenWidth = newScreenWidth;
                this.showHoverWindow = false;
                this.showPlanWindow = false;
                this.isMobile = (document.documentElement.clientWidth < 1024);
            }
        },
    }
})
</script>

<style scoped lang="scss">
.popup {
    @apply hidden;

    &.active {
        @apply flex items-center justify-center fixed w-full h-screen top-0 left-0 z-50;
    }
}

.popup-background {
    @apply absolute w-full h-screen bg-[rgba(0,0,0,0.7)] -z-10;
}

.popup-window {
    // general
    @apply w-fit pb-2 bg-background text-on-background rounded-xl;
    // desktop
    @apply md:px-2 md:max-w-[calc(498px+1rem)];

    .popup-window-header {
        @apply w-full flex justify-between items-center px-4 py-1;

        .popup-title-target {
            // general
            @apply text-on-surface-variant;
            // sp
            @apply text-xs mr-1;
            // desktop
            @apply md:text-sm md:mr-2;
        }

        .popup-island-name {
            @apply text-left font-bold grow min-w-0 max-w-[80%] leading-none py-1;
        }

        .close-button {
            @apply ml-auto p-0 text-xl bg-background border-none hover:bg-background drop-shadow-none;
        }
    }

    .comment-box {
        @apply w-full max-w-[clamp(0px,95vw,498px)] mt-2 text-left mx-auto px-2 leading-none;

        .comment-title {
            @apply text-xs md:text-sm text-on-surface-variant;
        }

        .comment-text {
            @apply text-sm px-1 leading-none md:text-base md:leading-none text-on-surface-variant;
        }
    }
}

.loading {
    // general
    @apply flex items-center justify-center;
    // sp
    @apply w-[100vw] h-[100vw];
    // desktop
    @apply max-w-[498px] max-h-[498px];

    .loading-circle {
        @apply w-1/6 h-1/6 text-surface-variant animate-spin fill-primary;
    }
}

#popup-island {
    // sp
    @apply w-[clamp(0px,100vw,498px)] mx-auto;
    // desktop
    @apply max-w-[498px] max-h-[498px];

    .row {
        @apply m-0 -mt-[0.1px] p-0 bg-black;
        display: grid;

        .cell {
            @apply w-full aspect-square;
        }

        &:nth-child(odd) {
            grid-template-columns: 1fr repeat(15, 2fr);
        }

        &:nth-child(even) {
            grid-template-columns: repeat(15, 2fr) 1fr;
        }

        .cell-is-selected {
            border: 1px solid white;
        }

        .cell-is-referenced {
            border: 1px solid red;
        }

        .left-padding {
            @apply w-full aspect-[1/2] z-10;
            background-image: url("/img/hakoniwa/hakogif/land0.gif");
            background-position: left;
        }

        .right-padding {
            @apply relative w-full aspect-[1/2] z-10;
            background-image: url("/img/hakoniwa/hakogif/land0.gif");
            background-position: right;

            .right-padding-text {
                @apply max-xs:hidden absolute left-1 leading-none text-white text-xs md:text-sm overflow-hidden z-10
            }
        }
    }

    .hover-window {
        @apply block absolute min-w-[200px] max-w-[200px] bg-black bg-opacity-50 p-1 text-white rounded-md border border-black -translate-x-1/2 z-30;

        .hover-window-header {
            @apply flex px-3 items-center;

            .hover-window-img {
                width:32px;
                height:32px;
                margin-right: 10px;
            }

            .hover-window-info {
                white-space: pre-line;
            }
        }

        .hover-window-plan {
            @apply text-sm text-left m-0 p-0;
        }

        .hover-window-plan:nth-child(2) {
            @apply border-t mt-3 pt-2 border-opacity-70 border-gray-500 ;
        }
    }

    .plan-window {
        @apply block absolute bg-surface-variant text-on-surface-variant w-fit max-lg:min-w-[230px] max-lg:max-w-[230px] max-lg:-translate-x-1/2 lg:max-w-[240px] rounded-md drop-shadow-xl text-left overflow-hidden max-md:text-sm border border-primary dark:border-primary-container z-30;
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
}

</style>
