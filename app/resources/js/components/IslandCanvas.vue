<template>
    <TresGroup>
        <TresPerspectiveCamera
            ref="camera"
            :position="[64, 192, 192] as Vector3"
        />
        <CameraControls
            v-bind="cameraControlsState"
            make-default
        />
        <TresGroup :position="[-64, 0, -64] as Vector3">
            <template v-for="terrain of store.terrains">
                <Suspense>
                    <IslandCell
                        :position="[terrain.data.point.x*8+((terrain.data.point.y%2-1)*4), 0, terrain.data.point.y*8] as Vector3"
                        :terrain="terrain"
                    ></IslandCell>
                </Suspense>
            </template>
        </TresGroup>

        <TresAmbientLight :intensity="2"/>
        <TresDirectionalLight
            :position="[192, 192, 192] as Vector3"
            :intensity="3"
        />
    </TresGroup>
</template>

<script setup lang="ts">
import {Vector3} from 'three'
import {CameraControls} from '@tresjs/cientos'

import {reactive} from 'vue'
import {useMainStore} from "../store/MainStore"
import IslandCell from "./IslandCell.vue"

let controls = null

const cameraControlsState = reactive({
    minDistance: 20,
    maxDistance: 200,
    maxPolarAngle: Math.PI / 2,
})

const store = useMainStore()

</script>

<style lang="scss" scoped>
.island-canvas {
    @apply w-full min-h-[496px] min-h-[496px] mb-4;
}
</style>
