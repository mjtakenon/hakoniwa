<template>
    <div id="island">
        <TresCanvas v-bind="gl" class="island-canvas">
            <TresPerspectiveCamera
                :position="[64, 192, 192] as Vector3"
            />
            <CameraControls
                v-bind="cameraControlsState"
                make-default
            />

            <Suspense>
                <IslandCanvas/>
            </Suspense>

            <TresAmbientLight :intensity="2"/>
            <TresDirectionalLight
                :position="[192, 192, 192] as Vector3"
                :intensity="3"
            />
        </TresCanvas>
        <HoverWindow></HoverWindow>
    </div>
</template>

<script setup lang="ts">
import {Terrain} from "../store/Entity/Terrain";
import {reactive} from "vue";
import {useMainStore} from "../store/MainStore";
import IslandCanvas from "./IslandCanvas.vue";
import {TresCanvas} from "@tresjs/core";
import {BasicShadowMap, NoToneMapping, SRGBColorSpace, Vector3} from "three";
import HoverCanvas from "./HoverCanvas.vue";
import {CameraControls} from "@tresjs/cientos";
import HoverWindow from "./HoverWindow.vue";

const gl = reactive({
    clearColor: '#888888',
    shadows: true,
    alpha: true,
    shadowMapType: BasicShadowMap,
    outputColorSpace: SRGBColorSpace,
    toneMapping: NoToneMapping,
})

const cameraControlsState = reactive({
    minDistance: 20,
    maxDistance: 200,
    maxPolarAngle: Math.PI / 2,
})

const store = useMainStore()

const getIslandTerrain = (x, y): Terrain => {
    return store.terrains.filter(function (item) {
        if (item.data.point.x === x && item.data.point.y === y) return true;
    }).pop()
}

</script>

<style lang="scss" scoped>

.island-canvas {
    @apply w-full min-h-[512px] min-h-[512px] mb-4;
}

#island {
    margin: 0 auto;
    @apply w-full md:min-w-[512px] max-w-[512px] mb-4;

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
                min-width: 32px;
                min-height: 32px;
                max-width: 32px;
                max-height: 32px;
                object-fit: cover;
                //margin-right: 10px;
            }

            .hover-window-info {
                white-space: pre-line;
            }
        }
    }
}
</style>
