<template>
    <TresPerspectiveCamera
        ref="camera"
        :position="store.getCells[getIslandTerrain(store.hoverCellPoint.x, store.hoverCellPoint.y).type].cameraPosition"
        :look-at="store.getCells[getIslandTerrain(store.hoverCellPoint.x, store.hoverCellPoint.y).type].position"
    />
    <template v-for="cell of store.getCells">
        <GLTFModel :path="cell.path" :position="cell.position"/>
    </template>
    <TresAmbientLight :intensity="2"/>
    <TresDirectionalLight
        :position="[20, 20, 20] as Vector3"
        :intensity="3"
    />
</template>

<script setup lang="ts">
import {Terrain} from "../store/Entity/Terrain"
import {Vector3} from "three";
import {useMainStore} from "../store/MainStore";
import {useTresContext} from "@tresjs/core";
import {GLTFModel} from "@tresjs/cientos";

const store = useMainStore()

let { renderer } = useTresContext()

renderer.value.setClearColor(0x000000, 0)

const getIslandTerrain = (x, y): Terrain => {
    return store.terrains.filter(function (item, idx) {
        if (item.data.point.x === x && item.data.point.y === y) return true;
    }).pop();
}

</script>

<style lang="scss" scoped>

</style>
