<template>
    <TresGroup :position="[-64, 0, -64] as Vector3">
        <template v-for="terrain of store.terrains">
            <IslandViewerCell
                :position="[terrain.data.point.x*8+((terrain.data.point.y%2-1)*4), models[terrain.type].scene.position.y, terrain.data.point.y*8] as Vector3"
                :terrain="terrain"
                :scene="models[terrain.type].scene.clone()"
            ></IslandViewerCell>
        </template>
    </TresGroup>
</template>

<script async setup lang="ts">
import {Box3, Vector3} from 'three'
import {useGLTF} from '@tresjs/cientos'
import {useMainStore} from "../store/MainStore"
import IslandViewerCell from "./IslandViewerCell.vue";

const store = useMainStore()

let models = {}

for (let type in store.getCells) {
    let model = await useGLTF(store.getCells[type].path, {draco: true})
    const size = (new Box3()).setFromObject(model.scene).getSize(new Vector3())
    model.scene.position.y += (size.y - 8) / 2
    models[type] = model
}

</script>

<style lang="scss" scoped>
.island-canvas {
    @apply w-full min-h-[496px] min-h-[496px] mb-4;
}
</style>
