<template>
    <TresCanvas v-bind="gl" class="island-canvas">
        <TresPerspectiveCamera
            ref="camera"
            :position="[64, 192, 192] as Vector3"
            :look-at="[0, 0, 0] as Vector3"
        />
        <CameraControls
            make-default
        />
        <TresGroup :position="[-64, 0, -64] as Vector3">
            <template v-for="y of store.hakoniwa.height">
                <template v-for="x of store.hakoniwa.width">
                    <Suspense>
                        <IslandCell
                            :position="[x*8+((y%2-1)*4), 0, y*8] as Vector3"
                            :terrain="getIslandTerrain(x-1, y-1)"
                        ></IslandCell>
                    </Suspense>
                </template>
            </template>
        </TresGroup>

        <TresAmbientLight :intensity="1"/>
        <TresDirectionalLight
            :position="[192, 192, 192] as Vector3"
            :intensity="2"
        />
    </TresCanvas>
</template>

<script setup lang="ts">
import {TresCanvas, TresInstance, useLoader, useRenderLoop, useTres} from '@tresjs/core'
import {BasicShadowMap, NoToneMapping, Raycaster, SRGBColorSpace, Vector2, Vector3} from 'three'
import {CameraControls, Box} from '@tresjs/cientos'

import {ref, shallowReactive, shallowRef, ShallowRef, watchEffect} from 'vue'
import {useGLTF, OrbitControls} from '@tresjs/cientos'
import {Terrain} from "../store/Entity/Terrain"
import {useMainStore} from "../store/MainStore"
import {storeToRefs} from "pinia"
import IslandCell from "./IslandCell.vue"

const objectRef: ShallowRef<TresInstance | null> = shallowRef(null)
const camera = ref(null)
const group = ref(null)

const gl = {
    clearColor: '#82DBC5',
    shadows: true,
    alpha: false,
    shadowMapType: BasicShadowMap,
    outputColorSpace: SRGBColorSpace,
    toneMapping: NoToneMapping,
}

const store = useMainStore()

const getIslandTerrain = (x, y): Terrain => {
    return store.terrains.filter(function (item, idx) {
        if (item.data.point.x === x && item.data.point.y === y) return true;
    }).pop();
}

const onMouseOverCell = (x, y, event: MouseEvent) => {
    console.log("over")
    // const offsetY = 25;
    // this.hoverWindowY = document.documentElement.clientHeight - event.pageY + offsetY;
    // this.hoverWindowX = event.pageX;
    //
    // // Screen Overflow Check
    // if(this.isMobile) {
    //     const windowSize = 200;
    //     const paddingOffset = 20;
    //     const leftEdge = this.hoverWindowX - (windowSize/2);
    //     const rightEdge = this.hoverWindowX + (windowSize/2);
    //     if (leftEdge < paddingOffset) {
    //         this.hoverWindowX += (-leftEdge) + paddingOffset;
    //     }
    //     else if (rightEdge > this.screenWidth) {
    //         this.hoverWindowX -= (rightEdge-this.screenWidth) + paddingOffset;
    //     }
    // }
    //
    // this.showHoverWindow = true;
    // this.hoverCellPoint.x = x;
    // this.hoverCellPoint.y = y;
}

const onMouseLeaveCell = () => {
    console.log("leave")
    // this.showHoverWindow = false;
}

const onMouseClick = (x, y) => {
    console.log("click")
    // this.store.selectedPoint.x = x;
    // this.store.selectedPoint.y = y;
}

</script>

<style lang="scss" scoped>
.island-canvas {
    @apply w-full min-h-[496px] min-h-[496px] mb-4;
}
</style>
