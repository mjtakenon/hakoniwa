<template>
    <template v-for="path in paths">
        <!--        <GLTFModel v-show="getIslandTerrain(store.hoverCellPoint.x, store.hoverCellPoint.y).type === path" :path="paths[path]"/>-->
<!--        <template v-show="getIslandTerrain(store.hoverCellPoint.x, store.hoverCellPoint.y).type === path" >-->
        <GLTFModel v-show="store.hoverCellType === path" :path="store.hoverCellPath" />
<!--        </template>-->
    <!--            v-for="node of nodesList[getIslandTerrain(store.hoverCellPoint.x, store.hoverCellPoint.y).type]"-->
    <!--            :position="positionList[getIslandTerrain(store.hoverCellPoint.x, store.hoverCellPoint.y).type]"-->
<!--        <primitive-->
<!--            v-if="store.showHoverWindow"-->
<!--            :object="nodesList[store.hoverCellType]"-->
<!--        >-->
<!--        </primitive>-->
    </template>
</template>

    <script setup lang="ts">
    import {Box3, Vector3} from 'three'
    import {GLTFModel, useGLTF} from '@tresjs/cientos'
    import {Terrain} from "../store/Entity/Terrain";
    import {useMainStore} from "../store/MainStore";
    import {storeToRefs} from "pinia";

    const {hakoniwa, terrains} = storeToRefs(useMainStore())

    interface Props {
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

    let nodesList = {};
    let positionList = {}

    for (let path in paths) {
        let {scene, nodes, animations, materials} = await useGLTF(paths[path], {draco: true})
        nodesList[path] = scene

        let box = new Box3()
        const size = box.setFromObject(scene).getSize(new Vector3())
        positionList[path] = new Vector3(0, (size.y - 8) / 2, 0)
    }

    const store = useMainStore()

    const getIslandTerrain = (x, y): Terrain => {
        return store.terrains.filter(function (item, idx) {
            if (item.data.point.x === x && item.data.point.y === y) return true;
        }).pop();
    }

    </script>

    <style lang="scss" scoped>

    </style>
