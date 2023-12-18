<template>
    <primitive :object="scene" ref="objectRef" :position="props.position"/>
</template>

<script setup lang="ts">
import {TresCanvas, TresInstance, useLoader, useRenderLoop} from '@tresjs/core'
import {BasicShadowMap, NoToneMapping, SRGBColorSpace, Vector3, Box3} from 'three'
import {CameraControls, Box} from '@tresjs/cientos'

import {ref, shallowReactive, shallowRef, ShallowRef, watchEffect} from 'vue'
import {useGLTF, OrbitControls} from '@tresjs/cientos'
import {Terrain} from "../store/Entity/Terrain";
import {useMainStore} from "../store/MainStore";
import {storeToRefs} from "pinia";

const {hakoniwa, terrains} = storeToRefs(useMainStore())

interface Props {
    terrain: Terrain
    position: array
}

const props = defineProps<Props>();

let path = {
    sea: '/img/hakoniwa/gltf/land0.gltf',
    shallow: '/img/hakoniwa/gltf/land14.gltf',
    plain: '/img/hakoniwa/gltf/land2.gltf',
    wasteland: '/img/hakoniwa/gltf/land1.gltf',
    forest: '/img/hakoniwa/gltf/land2.gltf',
    village: '/img/hakoniwa/gltf/land2.gltf',
    volcano: '/img/hakoniwa/gltf/land1.gltf',
    lake: '/img/hakoniwa/gltf/land14.gltf',
}

let {scene, nodes, animations, materials} = await useGLTF(path[props.terrain.type])

let box = new Box3();
const size = box.setFromObject(scene).getSize(new Vector3())
props.position[1]+=(size.y-8)/2

// const getScene = async () => {
//     let {scene, nodes} = await useGLTF(path)
//     return scene
// }

</script>

<style lang="scss" scoped>

</style>
