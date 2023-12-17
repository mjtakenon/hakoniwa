<template>
    <TresCanvas v-bind="gl" class="island-canvas">
        <TresPerspectiveCamera
            ref="camera"
            :position="[3, 3, 3]"
        />
<!--        <OrbitControls make-default />-->
        <CameraControls
            v-bind="controlsState"
            make-default
        />
        <TresMesh
            ref="boxRef"
            :scale="1"
        >
            <TresBoxGeometry :args="[1, 1, 1]" />
            <TresMeshNormalMaterial />
        </TresMesh>
        <TresAmbientLight :intensity="1" />
        <TresDirectionalLight
            :position="[0, 2, 4]"
            :intensity="2"
        />

    </TresCanvas>
</template>

<script setup lang="ts">
import {TresCanvas, TresInstance, useRenderLoop} from '@tresjs/core'
import {BasicShadowMap, NoToneMapping, SRGBColorSpace} from 'three'
import { CameraControls, Box } from '@tresjs/cientos'

import {OrbitControls} from '@tresjs/cientos'
import {ref, shallowReactive, shallowRef, ShallowRef, watchEffect} from 'vue'

const gl = {
    clearColor: '#82DBC5',
    shadows: true,
    alpha: false,
    shadowMapType: BasicShadowMap,
    outputColorSpace: SRGBColorSpace,
    toneMapping: NoToneMapping,
}

const boxRef: ShallowRef<TresInstance | null> = shallowRef(null)

const { onLoop } = useRenderLoop()

onLoop(({ delta, elapsed }) => {
    // if (boxRef.value) {
    //     boxRef.value.rotation.y += delta
    //     boxRef.value.rotation.z = elapsed * 0.2
    // }
})

const camera = ref(null)

watchEffect(() => {
    if (camera.value) {
        camera.value.lookAt(0, 0, 0)
    }
})

const transformState = shallowReactive({
    mode: 'translate',
    size: 1,
    axis: 'XY',
    showX: true,
    showY: true,
    showZ: true,
})
</script>

<style lang="scss" scoped>
.island-canvas {
    height: 500px;
    @apply md:min-w-[496px] max-w-[496px] w-full min-h-[496px] mb-4 ;
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
