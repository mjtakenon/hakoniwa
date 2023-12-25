<template>
    <primitive
        ref="objectRef"
        v-for="child of model.scene.children"
        :object="child"
        :position="props.position"
        @click="(intersection, pointerEvent) => onMouseClick(pointerEvent)"
        @pointer-enter="(intersection, pointerEvent) => onMouseOverCell(pointerEvent)"
        @pointer-leave="(intersection, pointerEvent) => onMouseLeaveCell(pointerEvent)"
        blocks-pointer-events
    ></primitive>
</template>

<script setup lang="ts">
import {Intersection, TresCanvas, TresInstance, useLoader, useRenderLoop} from '@tresjs/core'
import {BasicShadowMap, NoToneMapping, SRGBColorSpace, Vector3, Box3} from 'three'
import {CameraControls, Box} from '@tresjs/cientos'

import {ref, shallowReactive, shallowRef, ShallowRef, watchEffect} from 'vue'
import {useGLTF, GLTFModel} from '@tresjs/cientos'
import {Terrain} from "../store/Entity/Terrain";
import {useMainStore} from "../store/MainStore";

const store = useMainStore()

interface Props {
    terrain: Terrain
    position: Vector3
}

const props = defineProps<Props>();

let objectRef: ShallowRef<TresInstance | null> = shallowRef(null)

let model = await useGLTF(store.getCells[props.terrain.type].path, { draco: true })

const size = (new Box3()).setFromObject(model.scene).getSize(new Vector3())
props.position[1]+=(size.y-8)/2

const onMouseOverCell = (event: MouseEvent) => {
    const offsetY = 25;
    store.hoverWindowY = document.documentElement.clientHeight - event.pageY + offsetY;
    store.hoverWindowX = event.pageX;

    // Screen Overflow Check
    if(store.isMobile) {
        const windowSize = 200;
        const paddingOffset = 20;
        const leftEdge = store.hoverWindowX - (windowSize/2);
        const rightEdge = store.hoverWindowX + (windowSize/2);
        if (leftEdge < paddingOffset) {
            store.hoverWindowX += (-leftEdge) + paddingOffset;
        }
        else if (rightEdge > store.screenWidth) {
            store.hoverWindowX -= (rightEdge-store.screenWidth) + paddingOffset;
        }
    }

    store.showHoverWindow = true;
    store.hoverCellPoint = props.terrain.data.point

    store.changeHoverCellCameraFocus(props.terrain.type);
}

const onMouseLeaveCell = (event: MouseEvent) => {
    store.showHoverWindow = false;
}

const onMouseClick = (event: MouseEvent) => {
    store.hoverCellPoint = props.terrain.data.point;
}

const getIslandTerrain = (x, y): Terrain => {
    return store.terrains.filter(function (item, idx) {
        if (item.data.point.x === x && item.data.point.y === y) return true;
    }).pop();
}
</script>

<style lang="scss" scoped>

</style>
