<template>
    <TresPerspectiveCamera
        ref="camera"
        :position="[10, 10, 10] as Vector3"
        :look-at="[0, 0, 0] as Vector3"
    />
    <Suspense>
        <IslandCellPreview
            :position="[0, 0, 0] as Vector3"
        ></IslandCellPreview>
    </Suspense>
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
import IslandCellPreview from "./IslandCellPreview.vue";
import {useTresContext} from "@tresjs/core";

const store = useMainStore()

let { renderer } = useTresContext()

renderer.value.setClearColor(0x000000, 0)

</script>

<style lang="scss" scoped>

</style>
