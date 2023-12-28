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

        <!-- 選択セルカーソル -->
        <TresMesh
            ref="selectedBox"
            :scale="selectedBoxScale"
            :position="selectedBoxPosition"
            :visible="store.showPlanWindow"
        >
            <TresBoxGeometry :args="[1, 1, 1]"/>
            <template v-for="borderLine of borderLines">
                <TresMesh :scale="borderLine.scale" :position="borderLine.position">
                    <TresBoxGeometry :args="[1, 1, 1]"/>
                    <TresMeshBasicMaterial color="white"/>
                </TresMesh>
            </template>
        </TresMesh>

        <!-- プランセルカーソル -->
        <TresMesh
            ref="referencedBox"
            :scale="referencedBoxScale"
            :position="referencedBoxPosition"
            :visible="getReferencedPoint !== null"
        >
            <TresBoxGeometry :args="[1, 1, 1]"/>
            <template v-for="borderLine of borderLines">
                <TresMesh :scale="borderLine.scale" :position="borderLine.position">
                    <TresBoxGeometry :args="[1, 1, 1]"/>
                    <TresMeshBasicMaterial color="red"/>
                </TresMesh>
            </template>
        </TresMesh>
    </TresGroup>
</template>

<script async setup lang="ts">
import {Box3, Vector2, Vector3} from 'three'
import {useGLTF} from '@tresjs/cientos'
import {useMainStore} from "../store/MainStore"
import IslandEditorCell from "./IslandEditorCell.vue";
import {Terrain} from "../store/Entity/Terrain";
import {computed, onMounted, shallowRef} from "vue";

let selectedBox = shallowRef(null)
let referencedBox = shallowRef(null)

const store = useMainStore()

let models = {}

const borderLines = [
    {scale: [0.1, 1, 0.1] as Vector3, position: [-0.5, 0, -0.5] as Vector3},
    {scale: [0.1, 1, 0.1] as Vector3, position: [0.5, 0, -0.5] as Vector3},
    {scale: [0.1, 1, 0.1] as Vector3, position: [-0.5, 0, 0.5] as Vector3},
    {scale: [0.1, 1, 0.1] as Vector3, position: [0.5, 0, 0.5] as Vector3},
    {scale: [1, 0.05, 0.1] as Vector3, position: [0, 0.475, 0.5] as Vector3},
    {scale: [1, 0.05, 0.1] as Vector3, position: [0, 0.475, -0.5] as Vector3},
    {scale: [0.1, 0.05, 1] as Vector3, position: [0.5, 0.475, 0] as Vector3},
    {scale: [0.1, 0.05, 1] as Vector3, position: [-0.5, 0.475, 0] as Vector3},
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

    referencedBox.value.material.opacity = 0.1
    referencedBox.value.material.transparent = true
})

const getIslandTerrain = (x, y): Terrain => {
    return store.terrains.filter(function (item) {
        if (item.data.point.x === x && item.data.point.y === y) return true;
    }).pop()
}

const getModelSize = (type): Vector3 => {
    return (new Box3()).setFromObject(models[type].scene).getSize(new Vector3())
}

const selectedBoxScale = computed(() => {
    const selectedPoint = getReferencedPoint.value
    const selectedBoxScaleMargin = 0.1
    if (selectedPoint === null) {
        return new Vector3(0, 0, 0)
    }
    return new Vector3(store.cellSize + selectedBoxScaleMargin, getModelSize(getIslandTerrain(selectedPoint.x, selectedPoint.y).type).y + selectedBoxScaleMargin, store.cellSize + selectedBoxScaleMargin)
})

const selectedBoxPosition = computed(() => {
    const selectedPoint = store.selectedPoint
    const selectedBoxPositionMarginY = 4
    if (selectedPoint === null) {
        return new Vector3(0, 0, 0)
    }
    return new Vector3(selectedPoint.x * store.cellSize + (selectedPoint.y % 2 - 1) * store.cellSize / 2, (getModelSize(getIslandTerrain(selectedPoint.x, selectedPoint.y).type).y - 8) / 2 + selectedBoxPositionMarginY, selectedPoint.y * store.cellSize)
})

const getReferencedPoint = computed(() => {
    let referencedPlan = store.plans[store.selectedPlanNumber - 1]
    if (!referencedPlan.data.usePoint) {
        return null
    }
    if (referencedPlan.data.useTargetIsland && referencedPlan.data.targetIsland !== store.island.id) {
        return null
    }
    return new Vector2(referencedPlan.data.point.x, referencedPlan.data.point.y)
})

const referencedBoxScale = computed(() => {
    const referencedPoint = getReferencedPoint.value
    const referencedBoxScaleMargin = 0.05
    if (referencedPoint === null) {
        return new Vector3(0, 0, 0)
    }
    return new Vector3(store.cellSize + referencedBoxScaleMargin, getModelSize(getIslandTerrain(referencedPoint.x, referencedPoint.y).type).y + referencedBoxScaleMargin, store.cellSize + referencedBoxScaleMargin)
})

const referencedBoxPosition = computed(() => {
    const referencedPoint = getReferencedPoint.value
    const referencedBoxPositionMarginY = 4
    if (referencedPoint === null) {
        return new Vector3(0, 0, 0)
    }
    return new Vector3(referencedPoint.x * store.cellSize + (referencedPoint.y % 2 - 1) * store.cellSize / 2, (getModelSize(getIslandTerrain(referencedPoint.x, referencedPoint.y).type).y - 8) / 2 + referencedBoxPositionMarginY, referencedPoint.y * store.cellSize)
})

</script>

<style lang="scss" scoped>

</style>
