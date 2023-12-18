<template>
    <TresCanvas v-bind="gl" class="island-canvas">
        <TresPerspectiveCamera
            ref="camera"
            :position="[64, 192, 192]"
            :look-at="[0, 0, 0]"
        />
        <OrbitControls make-default/>
        <CameraControls
            make-default
        />
        <TresGroup :position="[-64, 0, -64]">
            <template v-for="y of store.hakoniwa.height">
                <template v-for="x of store.hakoniwa.width">
                    <Suspense>
                        <IslandCell :position="[x*8+((y%2-1)*4), 0, y*8]" :terrain="getIslandTerrain(x-1, y-1)"></IslandCell>
                    </Suspense>
                </template>
            </template>
        </TresGroup>

        <TresAmbientLight :intensity="1"/>
        <TresDirectionalLight
            :position="[0, 2, 4]"
            :intensity="2"
        />
    </TresCanvas>
</template>

<script setup lang="ts">
import {TresCanvas, TresInstance, useLoader, useRenderLoop} from '@tresjs/core'
import {BasicShadowMap, NoToneMapping, SRGBColorSpace, Vector3} from 'three'
import {CameraControls, Box} from '@tresjs/cientos'

import {ref, shallowReactive, shallowRef, ShallowRef, watchEffect} from 'vue'
import {useGLTF, OrbitControls} from '@tresjs/cientos'
import {Terrain} from "../store/Entity/Terrain";
import {useMainStore} from "../store/MainStore";
import {storeToRefs} from "pinia";
import IslandCell from "./IslandCell.vue";

const objectRef: ShallowRef<TresInstance | null> = shallowRef(null)

const gl = {
    clearColor: '#82DBC5',
    shadows: true,
    alpha: false,
    shadowMapType: BasicShadowMap,
    outputColorSpace: SRGBColorSpace,
    toneMapping: NoToneMapping,
}

// const {onLoop} = useRenderLoop()
// onLoop(({delta, elapsed}) => {
    // if (objectRef.value) {
    // objectRef.value.position.x = 10;
    // objectRef.value.position.y = 10;
    // objectRef.value.rotation.y += delta
    // objectRef.value.rotation.z = elapsed * 0.2
    // }
// })

// const camera = ref(null)

// watchEffect(() => {
//     if (camera.value) {
//         camera.value.lookAt(0, 0, 0)
//     }
// })

const transformState = shallowReactive({
    mode: 'translate',
    size: 1,
    axis: 'XY',
    showX: true,
    showY: true,
    showZ: true,
})

const store = useMainStore();

const getIslandTerrain = (x, y): Terrain => {
    return store.terrains.filter(function (item, idx) {
        if (item.data.point.x === x && item.data.point.y === y) return true;
    }).pop();
};
</script>

<style lang="scss" scoped>
.island-canvas {
    height: 500px;
    @apply md:min-w-[496px] max-w-[496px] w-full min-h-[496px] mb-4 ;
}
</style>
