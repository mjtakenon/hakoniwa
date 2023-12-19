<template>
    <div id="island">
        <Suspense>
            <TresCanvas v-bind="gl" class="island-canvas">
                <IslandCanvas
                    style="width:500px; height:500px;"
                ></IslandCanvas>
            </TresCanvas>
        </Suspense>
        <div v-if="store.showHoverWindow" class="hover-window" :style="{ bottom: store.hoverWindowY+'px', left: store.hoverWindowX+'px' }">
            <div class="hover-window-header">
                <img
                    class="hover-window-img"
                    :src="getIslandTerrain(store.hoverCellPoint.x, store.hoverCellPoint.y).data.image_path"
                >
                <div class="grow items-center hover-window-info">
                    {{ (getIslandTerrain(store.hoverCellPoint.x, store.hoverCellPoint.y).data.info) }}
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts">
import { Terrain } from "../store/Entity/Terrain";
import { defineComponent } from "vue";
import { useMainStore } from "../store/MainStore";
import IslandCanvas from "./IslandCanvas.vue";
import {TresCanvas} from "@tresjs/core";
import {BasicShadowMap, NoToneMapping, SRGBColorSpace} from "three";

export default defineComponent({
    components: {
        TresCanvas,
        IslandCanvas,
    },
    data() {
        return {
            showHoverWindow: false,
            hoverCellPoint: {
                "x": 0,
                "y": 0,
            },
            hoverWindowY: 170,
            hoverWindowX: 0,
            screenWidth: document.documentElement.clientWidth,
            isMobile: (document.documentElement.clientWidth < 1024),
            state: null,
            gl: {
                clearColor: '#888888',
                shadows: true,
                alpha: false,
                shadowMapType: BasicShadowMap,
                outputColorSpace: SRGBColorSpace,
                toneMapping: NoToneMapping,
            }

        }
    },
    setup() {
        const store = useMainStore();
        return { store };
    },
    mounted() {
        window.addEventListener("resize", this.onWindowSizeChanged);
    },
    unmounted() {
        window.removeEventListener("resize", this.onWindowSizeChanged);
    },
    methods: {
        getIslandTerrain(x, y): Terrain {
            return this.store.terrains.filter(function(item, idx){
                if (item.data.point.x === x && item.data.point.y === y) return true;
            }).pop();
        },
        onMouseOverCell(x, y, event: MouseEvent) {
            const offsetY = 25;
            this.hoverWindowY = document.documentElement.clientHeight - event.pageY + offsetY;
            this.hoverWindowX = event.pageX;

            // Screen Overflow Check
            if(this.isMobile) {
                const windowSize = 200;
                const paddingOffset = 20;
                const leftEdge = this.hoverWindowX - (windowSize/2);
                const rightEdge = this.hoverWindowX + (windowSize/2);
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
        onMouseLeaveCell() {
            this.showHoverWindow = false;
        },
        onMouseClick(x, y) {
            this.store.selectedPoint.x = x;
            this.store.selectedPoint.y = y;
        },
        onWindowSizeChanged() {
            this.showHoverWindow = false;
            this.isMobile = (document.documentElement.clientWidth < 1024);
            this.screenWidth = document.documentElement.clientWidth;
        },
    },
    computed: {},
    props: [],
});
</script>

<style lang="scss" scoped>

.island-canvas {
    @apply w-full min-h-[496px] min-h-[496px] mb-4;
}

#island {
    margin: 0 auto;
    @apply w-full md:min-w-[496px] max-w-[496px] mb-4;

    .row {
        @apply m-0 p-0 bg-black;
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
    }
}
</style>
