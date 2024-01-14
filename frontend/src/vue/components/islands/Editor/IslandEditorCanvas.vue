<template>
  <TresGroup
    :position="
      [
        -DEFAULT_CELL_SIZE * Math.floor(islandViewerStore.hakoniwa.width / 2),
        0,
        -DEFAULT_CELL_SIZE * Math.floor(islandViewerStore.hakoniwa.height / 2)
      ] as Vector3
    ">
    <template v-for="cell of props.terrain.cells">
      <IslandEditorCell
        :position="
          [
            cell.data.point.x * DEFAULT_CELL_SIZE + ((((cell.data.point.y + 1) % 2) - 1) * DEFAULT_CELL_SIZE) / 2,
            models[cell.type][cell.data.sub_type ?? 'default'].scene.position.y,
            cell.data.point.y * DEFAULT_CELL_SIZE
          ] as Vector3
        "
        :scale="models[cell.type][cell.data.sub_type ?? 'default'].scene.scale.x"
        :cell="cell"
        :scene="models[cell.type][cell.data.sub_type ?? 'default'].scene.clone()"></IslandEditorCell>
    </template>

    <!-- 選択セルカーソル -->
    <TresMesh
      ref="selectedBox"
      :scale="selectedBoxScale"
      :position="selectedBoxPosition"
      :visible="islandEditorStore.showPlanWindow">
      <TresBoxGeometry :args="[1, 1, 1]" />
      <template v-for="borderLine of borderLines">
        <TresMesh :scale="borderLine.scale" :position="borderLine.position">
          <TresBoxGeometry :args="[1, 1, 1]" />
          <TresMeshBasicMaterial color="white" />
        </TresMesh>
      </template>
    </TresMesh>

    <!-- プランセルカーソル -->
    <TresMesh
      ref="referencedBox"
      :scale="referencedBoxScale"
      :position="referencedBoxPosition"
      :visible="getReferencedPoint !== null && !islandEditorStore.isOpenPopup">
      <TresBoxGeometry :args="[1, 1, 1]" />
      <template v-for="borderLine of borderLines">
        <TresMesh :scale="borderLine.scale" :position="borderLine.position">
          <TresBoxGeometry :args="[1, 1, 1]" />
          <TresMeshBasicMaterial color="red" />
        </TresMesh>
      </template>
    </TresMesh>
  </TresGroup>
</template>

<script async setup lang="ts">
import { Box3, Vector2, Vector3 } from 'three'
import { useGLTF } from '@tresjs/cientos'
import IslandEditorCell from './IslandEditorCell.vue'
import { Terrain } from '$entity/Terrain'
import { computed, onMounted, shallowRef } from 'vue'
import { CellType, DEFAULT_CELL_SIZE, getCellPath, getCellSubTypes, getCellTypes } from '$entity/Cell.js'
import { useIslandEditorStore } from '$store/IslandEditorStore.js'
import { useIslandViewerStore } from '$store/IslandViewerStore.js'

let selectedBox = shallowRef(null)
let referencedBox = shallowRef(null)

const islandEditorStore = useIslandEditorStore()
const islandViewerStore = useIslandViewerStore()

interface Props {
  terrain: Terrain
}

const props = defineProps<Props>()

const borderLines = [
  { scale: [0.1, 1, 0.1] as Vector3, position: [-0.5, 0, -0.5] as Vector3 },
  { scale: [0.1, 1, 0.1] as Vector3, position: [0.5, 0, -0.5] as Vector3 },
  { scale: [0.1, 1, 0.1] as Vector3, position: [-0.5, 0, 0.5] as Vector3 },
  { scale: [0.1, 1, 0.1] as Vector3, position: [0.5, 0, 0.5] as Vector3 },
  { scale: [1, 0.05, 0.1] as Vector3, position: [0, 0.475, 0.5] as Vector3 },
  { scale: [1, 0.05, 0.1] as Vector3, position: [0, 0.475, -0.5] as Vector3 },
  { scale: [0.1, 0.05, 1] as Vector3, position: [0.5, 0.475, 0] as Vector3 },
  { scale: [0.1, 0.05, 1] as Vector3, position: [-0.5, 0.475, 0] as Vector3 }
]

let models = {}
for (let type of getCellTypes()) {
  models[type] = {}
  for (let subType of getCellSubTypes(type as CellType)) {
    let model = await useGLTF(getCellPath(type as CellType, subType), { draco: true })
    const size = new Box3().setFromObject(model.scene).getSize(new Vector3())
    model.scene.scale.x = DEFAULT_CELL_SIZE / size.x
    model.scene.scale.y = DEFAULT_CELL_SIZE / size.x
    model.scene.scale.z = DEFAULT_CELL_SIZE / size.x
    model.scene.position.y += (size.y * (DEFAULT_CELL_SIZE / size.x) - DEFAULT_CELL_SIZE) / 2
    models[type][subType] = model
  }
}

onMounted(() => {
  selectedBox.value.material.opacity = 0.1
  selectedBox.value.material.transparent = true

  referencedBox.value.material.opacity = 0.1
  referencedBox.value.material.transparent = true
})

const getCell = (x, y): Terrain => {
  return props.terrain.cells
    .filter(function (item) {
      if (item.data.point.x === x && item.data.point.y === y) return true
    })
    .pop()
}

const getModelSize = (type, subType): Vector3 => {
  return new Box3().setFromObject(models[type][subType].scene).getSize(new Vector3())
}

const selectedBoxScale = computed(() => {
  const selectedBoxScaleMargin = 0.1
  if (islandEditorStore.selectedPoint === null) {
    return new Vector3(0, 0, 0)
  }
  const cell = getCell(islandEditorStore.selectedPoint.x, islandEditorStore.selectedPoint.y)
  return new Vector3(
    DEFAULT_CELL_SIZE + selectedBoxScaleMargin,
    getModelSize(cell.type, cell.data.sub_type ?? 'default').y + selectedBoxScaleMargin,
    DEFAULT_CELL_SIZE + selectedBoxScaleMargin
  )
})

const selectedBoxPosition = computed(() => {
  const selectedPoint = islandEditorStore.selectedPoint
  const selectedBoxPositionMarginY = 4
  if (selectedPoint === null) {
    return new Vector3(0, 0, 0)
  }
  const cell = getCell(islandEditorStore.selectedPoint.x, islandEditorStore.selectedPoint.y)
  return new Vector3(
    selectedPoint.x * DEFAULT_CELL_SIZE + ((((selectedPoint.y + 1) % 2) - 1) * DEFAULT_CELL_SIZE) / 2,
    (getModelSize(cell.type, cell.data.sub_type ?? 'default').y - 8) / 2 + selectedBoxPositionMarginY,
    selectedPoint.y * DEFAULT_CELL_SIZE
  )
})

const getReferencedPoint = computed(() => {
  let referencedPlan = islandEditorStore.plans[islandEditorStore.selectedPlanNumber - 1]
  if (!referencedPlan.data.usePoint) {
    return null
  }
  if (referencedPlan.data.useTargetIsland && referencedPlan.data.targetIsland !== islandViewerStore.island.id) {
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
  const cell = getCell(referencedPoint.x, referencedPoint.y)
  return new Vector3(
    DEFAULT_CELL_SIZE + referencedBoxScaleMargin,
    getModelSize(cell.type, cell.data.sub_type ?? 'default').y + referencedBoxScaleMargin,
    DEFAULT_CELL_SIZE + referencedBoxScaleMargin
  )
})

const referencedBoxPosition = computed(() => {
  const referencedPoint = getReferencedPoint.value
  const referencedBoxPositionMarginY = 4
  if (referencedPoint === null) {
    return new Vector3(0, 0, 0)
  }
  const cell = getCell(referencedPoint.x, referencedPoint.y)
  return new Vector3(
    referencedPoint.x * DEFAULT_CELL_SIZE + ((((referencedPoint.y + 1) % 2) - 1) * DEFAULT_CELL_SIZE) / 2,
    (getModelSize(cell.type, cell.data.sub_type ?? 'default').y - 8) / 2 + referencedBoxPositionMarginY,
    referencedPoint.y * DEFAULT_CELL_SIZE
  )
})
</script>

<style lang="scss" scoped></style>
