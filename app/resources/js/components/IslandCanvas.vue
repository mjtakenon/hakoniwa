<template>
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

    <TresAmbientLight :intensity="2"/>
    <TresDirectionalLight
        :position="[192, 192, 192] as Vector3"
        :intensity="3"
    />
</template>

<script setup lang="ts">
import {
    TresCanvas,
    TresInstance,
    useLoader,
    useRenderLoop,
    useTres,
    useTresContext,
    useTresContextProvider
} from '@tresjs/core'
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
const canvas = ref(null)

const store = useMainStore()

const getIslandTerrain = (x, y): Terrain => {
    return store.terrains.filter(function (item, idx) {
        if (item.data.point.x === x && item.data.point.y === y) return true;
    }).pop();
}

</script>

<style lang="scss" scoped>
.island-canvas {
    @apply w-full min-h-[496px] min-h-[496px] mb-4;
}
</style>
