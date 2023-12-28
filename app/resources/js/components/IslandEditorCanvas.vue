<template>
    <TresGroup
        :position="[-store.cellSize*Math.floor(store.hakoniwa.width/2), 0, -store.cellSize*Math.floor(store.hakoniwa.height/2)] as Vector3">
        <template v-for="terrain of store.terrains">
            <IslandEditorCell
                :position="[terrain.data.point.x * store.cellSize + (terrain.data.point.y%2-1) * store.cellSize/2, models[terrain.type].scene.position.y, terrain.data.point.y * store.cellSize] as Vector3"
                :terrain="terrain"
                :scene="models[terrain.type].scene.clone()"
            ></IslandEditorCell>
        </template>

        <TresMesh
            ref="selectedBox"
            :scale="[store.cellSize+0.1, getModelSize(getIslandTerrain(store.selectedPoint.x, store.selectedPoint.y).type).y+0.1 , store.cellSize+0.1] as Vector3"
            :position="[store.selectedPoint.x * store.cellSize + (store.selectedPoint.y%2-1) * store.cellSize / 2, (getModelSize(getIslandTerrain(store.selectedPoint.x, store.selectedPoint.y).type).y - 8) / 2 + 4, store.selectedPoint.y * store.cellSize] as Vector3"
            :visible="store.showPlanWindow"
        >
            <TresBoxGeometry :args="[1, 1, 1]" ref="selectedBoxGeometry"/>
            <template v-for="selectedBoxLine of selectedBoxLines">
                <TresMesh :scale="selectedBoxLine.scale" :position="selectedBoxLine.position"> <TresBoxGeometry :args="[1, 1, 1]"/> <TresMeshBasicMaterial color="red"/> </TresMesh>
            </template>
        </TresMesh>
    </TresGroup>
</template>

<script async setup lang="ts">
import {Box3, EdgesGeometry, LineBasicMaterial, LineSegments, Vector3} from 'three'
import {useGLTF} from '@tresjs/cientos'
import {useMainStore} from "../store/MainStore"
import IslandEditorCell from "./IslandEditorCell.vue";
import {Terrain} from "../store/Entity/Terrain";
import {onMounted, ref, shallowRef} from "vue";

let selectedBox = shallowRef(null)

const store = useMainStore()

let models = {}

const selectedBoxLines = [
    {scale: [0.1,    1, 0.1] as Vector3, position: [-0.5,     0, -0.5] as Vector3},
    {scale: [0.1,    1, 0.1] as Vector3, position: [ 0.5,     0, -0.5] as Vector3},
    {scale: [0.1,    1, 0.1] as Vector3, position: [-0.5,     0,  0.5] as Vector3},
    {scale: [0.1,    1, 0.1] as Vector3, position: [ 0.5,     0,  0.5] as Vector3},
    {scale: [1,   0.05, 0.1] as Vector3, position: [ 0  , 0.475,  0.5] as Vector3},
    {scale: [1,   0.05, 0.1] as Vector3, position: [ 0  , 0.475, -0.5] as Vector3},
    {scale: [0.1, 0.05,   1] as Vector3, position: [ 0.5, 0.475,  0  ] as Vector3},
    {scale: [0.1, 0.05,   1] as Vector3, position: [-0.5, 0.475,  0  ] as Vector3},
]

for (let type in store.getCells) {
    let model = await useGLTF(store.getCells[type].path, {draco: true})
    const size = (new Box3()).setFromObject(model.scene).getSize(new Vector3())
    model.scene.position.y += (size.y - 8) / 2
    models[type] = model
}

onMounted(() => {
    selectedBox.value.material.opacity = 0.1
    selectedBox.value.material.transparent = true
})

const getIslandTerrain = (x, y): Terrain => {
    return store.terrains.filter(function (item) {
        if (item.data.point.x === x && item.data.point.y === y) return true;
    }).pop()
}

const getModelSize = (type): Vector3 => {
    return (new Box3()).setFromObject(models[type].scene).getSize(new Vector3())
}

</script>

<style lang="scss" scoped>
.island-canvas {
    @apply w-full min-h-[496px] min-h-[496px] mb-4;
}
</style>
