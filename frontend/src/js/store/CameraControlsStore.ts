import { defineStore } from 'pinia'
import { ref } from 'vue'
import { Camera } from 'three'

export const useCameraStore = defineStore('camera', () => {
  const canvas = ref<HTMLElement>()
  const camera = ref<Camera>()

  const init = () => {
    canvas.value.addEventListener('mousedown', () => {
      console.log('mousedown')
    })

    canvas.value.addEventListener('mouseup', () => {
      console.log('mouseup')
    })

    canvas.value.addEventListener('mouseenter', () => {
      console.log('mouseenter')
    })

    canvas.value.addEventListener('mouseleave', () => {
      console.log('mouseleave')
    })
  }

  return { canvas, camera, init }
})
