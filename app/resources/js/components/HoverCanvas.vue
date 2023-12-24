<template>
    <canvas ref="canvas">
    </canvas>
</template>

<script setup lang="ts">
import {Terrain} from "../store/Entity/Terrain"
import {AmbientLight, PerspectiveCamera, Scene, WebGLRenderer} from "three";
import {useMainStore} from "../store/MainStore";
import {useGLTF} from "@tresjs/cientos";
import {onMounted, ref} from "vue";

const store = useMainStore()

const width = 500
const height = 500

const scene = new Scene()

const canvas = ref(null)

const camera = new PerspectiveCamera(45, width / height)

let models = {}
for (let type in store.getCells) {
    models[type] = await useGLTF(store.getCells[type].path, {draco: true})
}

const light = new AmbientLight(0xffffff, 3)

let renderer: WebGLRenderer | null = null

onMounted(() => {

    renderer = new WebGLRenderer({
        canvas: canvas.value
    })

    renderer.setPixelRatio(window.devicePixelRatio)
    renderer.setSize(width, height)
    renderer.setClearColor(0x000000, 0)

    light.position.set(0, 0, 0).normalize()
    scene.add(light)

    for (let type in models) {
        models[type].scene.position.x = store.getCells[type].position[0]
        models[type].scene.position.y = store.getCells[type].position[1]
        models[type].scene.position.z = store.getCells[type].position[2]
        scene.add(models[type].scene)
    }

    camera.position.set(...store.getCells.sea.cameraPosition)
    camera.lookAt(store.getCells.sea.position[0], store.getCells.sea.position[1], store.getCells.sea.position[2])

    tick()
})

const getIslandTerrain = (x, y): Terrain => {
    return store.terrains.filter(function (item, idx) {
        if (item.data.point.x === x && item.data.point.y === y) return true;
    }).pop();
}

const tick = () => {
    for (let type in models) {
        models[type].scene.rotation.y += 0.01
    }
    renderer.render(scene, camera)
    requestAnimationFrame(tick)
}

</script>

<style lang="scss" scoped>

</style>
