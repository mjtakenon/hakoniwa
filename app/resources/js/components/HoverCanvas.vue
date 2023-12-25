<template>
    <canvas ref="canvas"/>
</template>

<script setup lang="ts">
import {AmbientLight, Box3, PerspectiveCamera, Scene, Vector3, WebGLRenderer} from "three";
import {useMainStore} from "../store/MainStore";
import {useGLTF} from "@tresjs/cientos";
import {onMounted, ref} from "vue";

const store = useMainStore()

const width = 500
const height = 500

const scene = new Scene()

const canvas = ref(null)

let models = {}
for (let type in store.getCells) {
    let model = await useGLTF(store.getCells[type].path, {draco: true})
    const size = (new Box3()).setFromObject(model.scene).getSize(new Vector3())
    model.scene.position.y += (size.y - 8) / 2
    models[type] = model
}

const light = new AmbientLight(0xffffff, 3)

store.hoverCellCamera = new PerspectiveCamera(40, width / height)

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

    store.changeHoverCellCameraFocus('sea')

    tick()
})

const tick = () => {
    for (let type in models) {
        models[type].scene.rotation.y += 0.01
    }
    renderer.render(scene, store.hoverCellCamera)
    requestAnimationFrame(tick)
}

</script>

<style lang="scss" scoped>

</style>
