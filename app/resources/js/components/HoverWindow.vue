<template>
    <div v-show="store.showHoverWindow" class="hover-window"
         :style="{ bottom: store.hoverWindowY+'px', left: store.hoverWindowX+'px' }">
        <div class="hover-window-header">
            <Suspense>
                <HoverCanvas class="hover-window-img"/>
            </Suspense>
            <div class="grow items-center hover-window-info">
                {{ (getIslandTerrain(store.hoverCellPoint.x, store.hoverCellPoint.y).data.info) }}
            </div>
        </div>
        <slot></slot>
    </div>
</template>

<script setup lang="ts">
import {useMainStore} from "../store/MainStore";
import HoverCanvas from "./HoverCanvas.vue";
import {Terrain} from "../store/Entity/Terrain";

const store = useMainStore()

const getIslandTerrain = (x, y): Terrain => {
    return store.terrains.filter(function (item) {
        if (item.data.point.x === x && item.data.point.y === y) return true;
    }).pop()
}

</script>

<style lang="scss" scoped>

.island-canvas {
    @apply w-full min-h-[496px] min-h-[496px] max-h-[496px] max-h-[496px];
}

#island {
    margin: 0 auto;
    @apply w-full md:min-w-[496px] max-w-[496px];

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
                width: 32px;
                height: 32px;
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
