<template>
    <primitive
        v-for="node of nodes" :object="node" :position="props.position"
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
import {storeToRefs} from "pinia";

const {hakoniwa, terrains} = storeToRefs(useMainStore())

interface Props {
    terrain: Terrain
    position: Vector3
}

const props = defineProps<Props>();

let paths = {
    sea: '/img/hakoniwa/gltf/land0.gltf',
    shallow: '/img/hakoniwa/gltf/land14.gltf',
    plain: '/img/hakoniwa/gltf/land2.gltf',
    wasteland: '/img/hakoniwa/gltf/land1.gltf',
    forest: '/img/hakoniwa/gltf/land2.gltf',
    village: '/img/hakoniwa/gltf/land3.gltf',
    volcano: '/img/hakoniwa/gltf/volcano.gltf',
    lake: '/img/hakoniwa/gltf/land14.gltf',
}

let {scene, nodes, animations, materials} = await useGLTF(paths[props.terrain.type], { draco: true })

let box = new Box3()
const size = box.setFromObject(scene).getSize(new Vector3())
props.position[1]+=(size.y-8)/2

const store = useMainStore()

const onMouseOverCell = (event: MouseEvent) => {
    console.log("over")
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

    store.hoverCellType = props.terrain.type
    store.hoverCellPath = paths[props.terrain.type]

    console.log(store.hoverCellType)
}

const onMouseLeaveCell = (event: MouseEvent) => {
    console.log("leave")
    store.showHoverWindow = false;
}

const onMouseClick = (event: MouseEvent) => {
    console.log("click")
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
