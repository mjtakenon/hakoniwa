<template>
    <primitive
        v-for="child of props.scene.children"
        :object="child"
        :position="props.position"
        @click="(intersection, pointerEvent) => onClickCell(pointerEvent)"
        @pointer-enter="(intersection, pointerEvent) => onMouseOverCell(pointerEvent)"
        @pointer-move="(intersection, pointerEvent) => onMouseOverCell(pointerEvent)"
        @pointer-leave="(intersection, pointerEvent) => onMouseLeaveCell(pointerEvent)"
        blocks-pointer-events
    ></primitive>
</template>

<script setup lang="ts">
import {Object3D, Vector3} from 'three'
import {Terrain} from "../store/Entity/Terrain";
import {useMainStore} from "../store/MainStore";

const store = useMainStore()

interface Props {
    terrain: Terrain
    position: Vector3
    scene: Object3D
}

const props = defineProps<Props>();

const onMouseOverCell = (event: MouseEvent) => {
    onMouseMoveCell(event)

    store.showHoverWindow = true;
    store.hoverCellPoint = props.terrain.data.point

    store.changeHoverCellCameraFocus(props.terrain.type);
}

const onMouseMoveCell = (event: MouseEvent) => {
    const offsetY = 25;
    store.hoverWindowY = document.documentElement.clientHeight - event.pageY + offsetY;
    store.hoverWindowX = event.pageX;

    // Screen Overflow Check
    if (store.isMobile) {
        const windowSize = 200;
        const paddingOffset = 20;
        const leftEdge = store.hoverWindowX - (windowSize / 2);
        const rightEdge = store.hoverWindowX + (windowSize / 2);
        if (leftEdge < paddingOffset) {
            store.hoverWindowX += (-leftEdge) + paddingOffset;
        } else if (rightEdge > store.screenWidth) {
            store.hoverWindowX -= (rightEdge - store.screenWidth) + paddingOffset;
        }
    }
}

const onMouseLeaveCell = (event: MouseEvent) => {
    store.showHoverWindow = false;
}

const onClickCell = (event: MouseEvent) => {

    if (store.showPlanWindow &&
        store.selectedPoint.x === props.terrain.data.point.x &&
        store.selectedPoint.y === props.terrain.data.point.y
    ) {
        store.showPlanWindow = false;
        console.log(store.showPlanWindow)
        return;
    }
    store.selectedPoint.x = props.terrain.data.point.x
    store.selectedPoint.y = props.terrain.data.point.y
    store.showPlanWindow = true;

    if (store.isMobile) {
        store.planWindowX = event.pageX;
        const offsetX = 15;
        const offsetY = 30;
        const elementWidth = 230;
        const leftEdge = store.planWindowX - (elementWidth / 2);
        const rightEdge = store.planWindowX + (elementWidth / 2);
        if (leftEdge < offsetX) {
            store.planWindowX += (-leftEdge) + offsetX;
        } else if (rightEdge > store.screenWidth) {
            store.planWindowX -= (rightEdge - store.screenWidth) + offsetX;
        }
        store.planWindowY = event.pageY + offsetY;
    } else {
        const offset = 15;
        store.planWindowX = event.pageX + offset;
        store.planWindowY = event.pageY + offset;
    }

    console.log(store.showPlanWindow)
}


</script>

<style lang="scss" scoped>

</style>
