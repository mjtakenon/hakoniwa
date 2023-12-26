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
</style>
